# Laravel ER Diagram

こんな感じのER図を、laravel/EloquentのhasManyやbelongsToを解析して生成します。

![image.png](https://qiita-image-store.s3.amazonaws.com/0/255465/babf59b5-0b4c-9984-8e2a-36c81194730f.png)

表示系は、まるっと Ondřej Žára さんの「WWW SQL Designer」を利用しています  
http://ondras.zarovi.cz/#projects

# 使い方

## ファイルのコピー

- `app\Http\ER` ディレクトリを、自分のapp内にコピー
- `routes/web.php` 内の記述を、自分の設定にコピー
- `app\Http\ER\ERDiagramController.php` 内の `ENTITY_PATH` に、eloquentの配置ディレクトリを設定します。（デフォルトは App\Entities）

## ブラウザアクセス

http://localhost/er/main/Hoge  
(Hoge は Entity 名) のような形式で、ブラウザにアクセスしてください。  
Hoge を起点としたER図が生成されます。

各エンティティをクリックすると、リンク線が太字になってわかりやすくなります。

