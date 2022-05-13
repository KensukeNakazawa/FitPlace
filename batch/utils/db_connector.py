# coding: utf-8
import os
import MySQLdb


class DbConnector:

    def __init__(self) -> None:
        self.conn = MySQLdb.connect(
            user=os.environ.get('DB_USERNAME'),
            passwd=os.environ.get('DB_PASSWORD'),
            host=os.environ.get('DB_HOST'),
            db=os.environ.get('DB_DATABASE'),
            use_unicode=True,
            charset='utf8'
        )
        self.cursor = self.conn.cursor()

    def execute(self, sql: str) -> tuple:
        """入力したクエリを実行する
        Args:
            sql(str): 実行するクエリ
        Returns: 
            rows(tuple): SQLにより、取得したデータ
        """
        self.cursor.execute(sql)
        return self.cursor.fetchall()
