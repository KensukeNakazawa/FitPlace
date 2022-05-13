# coding: utf-8
from datetime import datetime, timedelta, timezone

import io
import os
import sys

sys.path.append(os.path.join(os.path.dirname(__file__), os.pardir))

from dotenv import load_dotenv
import matplotlib
import matplotlib.pyplot as plt
from matplotlib import font_manager

from batch.utils.db_connector import DbConnector
from batch.utils.line_notify import LineNotify

NOTIFY_DATE_FORMAT = "%Y年%-m月%-d日"
SQL_START_DATE_FORMAT = "%Y-%m-%d 00:00:00"
SQL_END_DATE_FORMAT = "%Y-%m-%d 23:59:59"

# グラフの色、ThisWeekExerciseVolumeGraphComponentと同じ色
PIE_GRAPH_COLORS = [
    '#4c6cb3', 
    '#007bbb', 
    '#89c3eb', 
    '#abced8', 
    '#dbd0e6', 
    '#a0d8ef', 
    '#2ca9e1', 
    '#eaf4fc'
]


def main():
    db_connector = DbConnector()
    line_notify = LineNotify()
    
    # 日本時間で取得する
    JST = timezone(timedelta(hours=+9), 'JST')
    today = datetime.now(JST)
    # 土曜日
    end_day = today - timedelta(days=1)
    # 月曜日
    start_day = end_day - timedelta(days=6)

    user_access_token_sql  = get_user_access_token_sql() 
    user_access_tokens = db_connector.execute(sql=user_access_token_sql)

    user_start_day = start_day.strftime(NOTIFY_DATE_FORMAT)
    user_end_day = end_day.strftime(NOTIFY_DATE_FORMAT)
    notify_text = """{start_day}から{end_day}までの1週間のトレーニングのサマリー(部位別)です。"""\
        .format(start_day=user_start_day, end_day=user_end_day)

    # DB用の日付フォーマット
    db_start_day = start_day.strftime(SQL_START_DATE_FORMAT)
    db_end_day = end_day.strftime(SQL_END_DATE_FORMAT)

    # ユーザーごとに処理
    for user_access_token in user_access_tokens:
        user_id = user_access_token[0]
        user_name = user_access_token[1]
        access_token = user_access_token[2]

        sql = get_exercise_sql(db_start_day, db_end_day, user_id)

        rows = db_connector.execute(sql=sql)
        image = get_pie_graph(rows, user_start_day, user_end_day)

        notify_text = "\n{}さん\n".format(user_name) + notify_text

        notify_text += get_notify_content(rows)
        
        line_notify.post_notify(
            notify_text, 
            access_token=access_token,
            image_file=image
        )


def get_user_access_token_sql() -> str:
    return """
        SELECT users.id, users.name, line_notifies.access_token 
        FROM line_notifies
        INNER JOIN users
        ON line_notifies.user_id = users.id
        WHERE line_notifies.access_token IS NOT NULL
    """


def get_exercise_sql(start_day: str, end_day: str, user_id: int = 1) -> str:
    return """
        SELECT body_parts.name, SUM(exercise_details.weight), COUNT(exercise_details.id) FROM exercises
        INNER JOIN exercise_details
        ON exercises.id = exercise_details.exercise_id
        INNER JOIN exercise_types
        ON exercises.exercise_type_id = exercise_types.id
        INNER JOIN body_parts
        ON exercise_types.body_part_id = body_parts.id
        WHERE exercises.exercise_at >= '{start_day}'
        AND exercises.exercise_at <= '{end_day}'
        AND exercises.user_id = {user_id}
        GROUP BY exercise_types.body_part_id
        ORDER BY exercise_types.body_part_id
    """.format(start_day=start_day, end_day=end_day, user_id=user_id)


def get_pie_graph(rows, start_day: str, end_day: str):
    """円グラフを設定して、取得する(グラフは画像にして、バイトのデータをメモリに乗せる)
    """
    values = [row[1] for row in rows]
    labels = [row[0] + ' (' + str(row[1]) + 'kg)' for row in rows]
    font_file = os.environ.get('PYTHON_FONT_PATH')
    font_family = os.environ.get('PYTHON_FONT_FAMILY')
    
    font_manager.fontManager.addfont(font_file)

    matplotlib.rc('font', family=font_family)
    
    fig = plt.figure()
    fig.text(0.43, 0.05, 'Made By FitPlace')
    plt.title("{}から{}までの部位別トレーニング".format(start_day, end_day))

    plt.pie(
        values, 
        labels=labels, 
        colors=PIE_GRAPH_COLORS, 
        autopct="%1.1f%%", 
        startangle=270
    )

    format = "jpeg"
    sio = io.BytesIO()
    plt.savefig(sio, format=format)
    plt.close(fig)

    return sio.getvalue()

def get_notify_content(rows):
    body_parts = [row[0] for row in rows]
    sets = [row[2] for row in rows]
    result_content = ""
    for index, body_part in enumerate(body_parts):
        result_content += "\n・" + body_part + ": " + str(sets[index]) + "セット"
    return result_content


if __name__ == '__main__':
    load_dotenv()
    main()
