<?php
namespace App\Utilities;

class CustomDFSHelper
{
    private static $matrix = [];
    private static $initialPoint = ['x' => 0,'y' => 0];
    private static $rows = 0;
    private static $columns = 0;
    private static $weightNode = 0;

    public static function getWeightNode(array $matrixData, int $x, int $y): int
    {
        static::$weightNode = 0;
        static::$matrix = $matrixData;
        static::$rows = count($matrixData);
        static::$columns = count($matrixData[0]);
        static::$initialPoint = ['x' => $x,'y' => $y];
        self::DFS($x, $y, 1, 0);//busqueda a la derecha del nodo
        self::DFS($x, $y, 0, 1);//busqueda hacia abajo del nodo
        self::DFS($x, $y, -1, 0);//busqueda a la izquierda del nodo
        self::DFS($x, $y, 0, -1);//busqueda hacia arriba del nodo

        return static::$weightNode;
    }
    private static function DFS(int $x, int $y, int $factorX, int $factorY){
        
        //Varificamos que este dentro del rango y sea nodo valido
        if($x < 0 || $y < 0 || $x >= static::$columns || $y >= static::$rows || static::$matrix[$y][$x] == '1'){
            return;
        }

        //Si no es el nodo original, contar
        if($x != static::$initialPoint['x'] || $y != static::$initialPoint['y']){
            static::$weightNode++;
        }
        //avanzar dentro de la matriz
        self::DFS($x+$factorX, $y+$factorY, $factorX, $factorY);
        return;
    }

    public static function setIluminatedArea(array &$matrix, $x, $y){
        static::$rows = count($matrix);
        static::$columns = count($matrix[0]);
        self::searchIluminatedNodes($matrix,$x, $y, 1, 0);//busqueda a la derecha del nodo
        self::searchIluminatedNodes($matrix,$x, $y, 0, 1);//busqueda hacia abajo del nodo
        self::searchIluminatedNodes($matrix,$x, $y, -1, 0);//busqueda a la izquierda del nodo
        self::searchIluminatedNodes($matrix,$x, $y, 0, -1);//busqueda hacia arriba del nodo
    }

    private static function searchIluminatedNodes(array &$matrix,int $x, int $y, int $factorX, int $factorY){
        //Varificamos que este dentro del rango y sea nodo valido
        if($x < 0 || $y < 0 || $x >= static::$columns || $y >= static::$rows || $matrix[$y][$x]['isWall']){
            return;
        }
        $matrix[$y][$x]['illuminated'] = true;
        //avanzar dentro de la matriz
        self::searchIluminatedNodes($matrix, $x+$factorX, $y+$factorY, $factorX, $factorY);
        return;
    }
}