<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 h-screen">

    <div class="flex items-center justify-center h-full">
        <div class="bg-white p-8 rounded-lg shadow-md w-3/4 md:w-2/3 lg:w-1/2">
            <h1 class="text-2xl mb-4">LINE Emoji Download Helper</h1>
            <form action="{{ route('download') }}" method="POST" class="space-y-4">
                @csrf
                <div class="border rounded overflow-hidden p-2 space-y-2">
                    <span class="text-red-500 text-xs">※必須</span>
                    @if ($errors->has('target_url'))
                        <div class="text-red-500 text-xs">
                            ▶ {{ $errors->first('target_url') }}
                        </div>
                    @endif
                    <input type="text" name="target_url" class="outline-none px-4 py-2 w-full"
                        placeholder="URLを入力...">
                    <span
                        class="text-gray-600 text-xs">例:https://store.line.me/emojishop/product/6319855a83603436e7d1951d/ja</span>
                    <p class="text-sm text-gray-600 mt-1">
                        ・LINEスタンプには対応しておりません。
                    </p>
                </div>
                <div class="border rounded overflow-hidden p-2 space-y-2">
                    <span class="text-red-500 text-xs">※必須</span>
                    @if ($errors->has('target_prefix'))
                        <div class="text-red-500 text-xs">
                            ▶ {{ $errors->first('target_prefix') }}
                        </div>
                    @endif
                    <input type="text" name="target_prefix" class="outline-none px-4 py-2 w-full"
                        placeholder="接頭語を入力...">
                    <span class="text-gray-600 text-xs">例:chiikawa</span>
                    <p class="text-sm text-gray-600 mt-1">
                        ・半角英数字のみ<br>
                        ・{接頭語}_{画像ファイル名}の形になります
                    </p>
                </div>
                <x-primary-button class="w-full justify-center">ダウンロード</x-primary-button>
            </form>


            <p class="bg-gray-100 rounded p-4 mt-4 shadow">
                アップロード拡張機能
                <a href="https://chrome.google.com/webstore/detail/neutral-face-emoji-tools/anchoacphlfbdomdlomnbbfhcmcdmjej"
                    class="text-blue-500 hover:underline">こちら</a>。<br>
                ChromeでSlackを開き、左上のサーバー名から「その他管理項目」＞「以下をカスタマイズ」の順に進む。これで画面上部に新しい「Bulk Emoji Uploader」が表示される。
            </p>
        </div>
    </div>
</body>

</html>
