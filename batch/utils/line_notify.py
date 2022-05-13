"""
LINE Notify を使うクラスを定義
"""
# coding: utf-8
import requests


class LineNotify():
    """
    LINE通知を行うクラス
    """

    def __init__(self) -> None:
        self.url = "https://notify-api.line.me/api/notify"

    def post_notify(self, message, access_token: str= None, image_file: bytes=None) -> None:
        """Lineにメッセージを送る
        Args:
            message(str): 送信したいメッセージ
            access_token(str): 送信対象ユーザーのアクセストークン
            image_file(bytes): 送信したい画像データバイナリ
        """
        headers = {'Authorization': 'Bearer ' + access_token}
        payload = {'message': message}
        files = {"imageFile": image_file}
        requests.post(self.url, headers=headers, params=payload, files=files)

