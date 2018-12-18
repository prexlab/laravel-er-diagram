<?php
namespace App\Http\Controllers\ER;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ERDiagramController extends Controller
{
    const ENTITY_PATH = 'App\Entities\\';

    private $depth = [];
    private $count = [];

    public function __construct()
    {
        \Debugbar::disable();
        View::addLocation(__DIR__.'/views');
    }

    /**
     * 表示画面
     *
     * @param $entity
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function main($entity, Request $request)
    {
        if ($request->refresh) {
            $this->refresh($entity);
        }

        return view('sqldesigner/index', compact('entity'));
    }

    private function refresh($entity)
    {
        unlink(storage_path($entity.'.json'));
    }

    /**
     * エンティティを指定して、SQL Designer用のSQLファイルを得る
     *
     * @param $entity
     * @param Request $request
     *
     * @throws \ReflectionException
     * @return mixed
     */
    public function xml($entity, Request $request)
    {
        ob_end_flush();

        $path = storage_path($entity.'.json');

        if (!is_file($path) || $request->refresh) {
            $ret = $this->getER(self::ENTITY_PATH.$entity);
            $tables = $this->getTableInfo($ret);

            $depth = $this->depth;
            $count = $this->count;

            file_put_contents($path, json_encode(compact('tables', 'depth', 'count')));
        }

        $ret = json_decode(file_get_contents($path), 1);

        return response(view('er', $ret))
                ->withHeaders([
                    'Content-Type' => 'text/xml',
                ]);
    }

    /**
     * SQL Designer用のviewに渡せるデータ形式を作る
     *
     * @param $info
     *
     * @return array
     */
    private function getTableInfo($info): array
    {

        //Entity名抽出ミニ関数
        $conv = function ($str) {
            return str_replace(self::ENTITY_PATH, '', $str);
        };

        $ret = [];
        foreach ($info as $entity => $relations) {
            $entityName = $conv($entity);

            $ret[$entityName]['id'] = ['column' => 'id', 'type' => 'integer'];

            foreach ($relations as $r) {
                $relationEntity = $conv($r['entity']);

                if (in_array($r['relation'], ['hasMany', 'hasOne'])) {
                    $col = snake_case($entityName).'_id';

                    if (empty($ret[$relationEntity])) {
                        $ret[$relationEntity]['id'] = ['column' => 'id', 'type' => 'integer'];
                    }

                    $ret[$relationEntity][$col] = [
                        'column' => $col,
                        'type' => 'integer',
                        'relation' => [
                            'table' => $entityName,
                            'column' => 'id',
                            'type' => $r['relation'],
                        ],
                    ];
                } else {
                    $col = snake_case($relationEntity).'_id';

                    $ret[$entityName][$col] = [
                        'column' => $col,
                        'type' => 'integer',
                        'relation' => [
                            'table' => $relationEntity,
                            'column' => 'id',
                            'type' => $r['relation'],
                        ],
                    ];
                }
            }
        }

        return $ret;
    }

    /**
     * メソッドのコード文字列を取得する
     *
     * @param $method
     *
     * @return string
     */
    private function getCode($method): string
    {
        $code = file_get_contents($method->getFileName());
        $lines = preg_split('/[\n\r]/', $code);
        $lines = array_slice($lines, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        $ret = implode("\n", $lines);

        //簡易的なコメント排除。行頭からの１行コメントを除去
        $ret = preg_replace('&^\s+(#|//).+&m', '', $ret);

        return $ret;
    }

    /**
     * 再帰的にリフレクションして、リレーション関係を取得する
     *
     * @param $entity
     * @param array $array
     * @param mixed $depth
     *
     * @throws \ReflectionException
     * @return array
     */
    private function getER($entity, $array = [], $depth = 0): array
    {
        if (empty($array[$entity])) {
            $array[$entity] = [];
        } else {
            return $array;
        }

        $conv = function ($str) {
            return str_replace(self::ENTITY_PATH, '', $str);
        };

        $this->depth[$conv($entity)] = $depth;
        if (empty($this->count[$depth])) {
            $this->count[$depth] = [];
        }
        $this->count[$depth][] = $conv($entity);

        //echo '.';
        //flush();

        $reflection = new \ReflectionClass($entity);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $code = $this->getCode($method);
            if (preg_match('/->(hasMany|hasOne|belongsToMany|belongsTo).+(App\\\\Entities\\\\[a-zA-Z0-9]+)/', $code, $mc)) {
                $array[$entity][] = ['relation' => $mc[1], 'entity' => $mc[2]];
            }
        }

        $depth++;
        foreach ($array[$entity] as $r) {
            $array = $this->getER($r['entity'], $array, $depth);
        }

        return $array;
    }
}
