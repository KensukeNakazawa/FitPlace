<?php

// ユーザーの初期データ関係

$body_parts = [
    '胸',
    '肩',
    '上腕三頭筋',
    '上腕二頭筋',
    '背中',
    '腹筋',
    '脚 & 臀筋',
    'その他'
];

$exercise_types = [
    ['ベンチプレス', 'インクラインベンチプレス', 'ディップス', 'ケーブルクロス'],
    ['ショルダープレス', 'サイドレイズ', 'リアレイズ'],
    ['トライセップエクステンション', 'スカルクラッシャー'],
    ['バーベルカール', 'ダンベルカール', 'インクラインダンベルカール'],
    ['懸垂', 'デッドリフト'],
    ['ハンギングレッグレイズ', 'ローラー'],
    ['スクワット', 'ブルガリアンスクワット', 'ヒップスラスト'],
    []
];

return [
    'BODY_PARTS' => $body_parts,
    'EXERCISE_TYPES' => $exercise_types
];