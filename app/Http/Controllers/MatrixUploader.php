<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CustomDFSHelper;

class MatrixUploader extends Controller
{
    public function uploadFile(Request $request){
        $file = $request->matrix;
        $extension = $file->getClientOriginalExtension();
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $matrix = [];
        foreach($lines AS $line){
            $cells = str_split($line);
            $matrix[] = $cells;
        }

        echo json_encode($matrix);
    }

    public function analyze(Request $request){
        //Obtenemos la matriz tal como fue cargada
        $plainMatrix = $request->room;

        $x = $y = null;
    
        //Obtenemos la matriz de pesos de cada nodo
        $analyzedMatrix = [];
        $pendingBulbsPositions = [];
        foreach($plainMatrix AS $lineIndex => $lines){
            $analyzedMatrix[] = [];
            foreach($lines AS $columnIndex => $column){
                if($column == '0'){
                    $x = $columnIndex;
                    $y = $lineIndex;

                    $weightNode = CustomDFSHelper::getWeightNode($plainMatrix,$x, $y);
                    if($weightNode == 1){
                        $pendingBulbsPositions[] = ['x' => $x, 'y' => $y];
                    }
                    $analyzedMatrix[$lineIndex][] = [
                        'isWall' => false,
                        'isLightBulb' => $weightNode == 0,
                        'weight' => $weightNode,
                        'illuminated' => $weightNode == 0,
                    ];
                }else{
                    $analyzedMatrix[$lineIndex][] = ['isWall' => true, 'illuminated' => true];
                }
            }
        }

        //Verificamos los nodos que solo tienen una unica salida, ahi hay que poner
        //una bombilla en la salida
        foreach($pendingBulbsPositions AS $bulbPosition){
            $position = ['x' => 0, 'y' => 0];
            //primero checamos a la derecha
            if(isset($analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']+1]) 
            && !$analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']+1]['isWall']
            && !$analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']+1]['illuminated']){
                $analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']+1]['isLightBulb'] = true;
                $position = ['x' => $bulbPosition['x']+1, 'y' => $bulbPosition['y']];
            }else if(isset($analyzedMatrix[$bulbPosition['y']+1][$bulbPosition['x']]) //abajo
            && !$analyzedMatrix[$bulbPosition['y']+1][$bulbPosition['x']]['isWall']
            && !$analyzedMatrix[$bulbPosition['y']+1][$bulbPosition['x']]['illuminated']){
                $analyzedMatrix[$bulbPosition['y']+1][$bulbPosition['x']]['isLightBulb'] = true;
                $position = ['x' => $bulbPosition['x'], 'y' => $bulbPosition['y']+1];
            }else if(isset($analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']-1]) //izquierda
            && !$analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']-1]['isWall']
            && !$analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']-1]['illuminated']){
                $analyzedMatrix[$bulbPosition['y']][$bulbPosition['x']-1]['isLightBulb'] = true;
                $position = ['x' => $bulbPosition['x']-1, 'y' => $bulbPosition['y']];
            }else if(isset($analyzedMatrix[$bulbPosition['y']-1][$bulbPosition['x']]) //arriba
            && !$analyzedMatrix[$bulbPosition['y']-1][$bulbPosition['x']]['isWall']
            && !$analyzedMatrix[$bulbPosition['y']-1][$bulbPosition['x']]['illuminated']){
                $analyzedMatrix[$bulbPosition['y']-1][$bulbPosition['x']]['isLightBulb'] = true;
                $position = ['x' => $bulbPosition['x'], 'y' => $bulbPosition['y']-1];
            }

            //Marcamos como iluminados los nodos contiguos
            CustomDFSHelper::setIluminatedArea($analyzedMatrix, $position['x'], $position['y']);
        }
        //Definir donde iran los focos en base a los pesos de cada nodo segun sus relaciones
        $totNodes = count($analyzedMatrix) * count($analyzedMatrix[0]);
        $i=0;
        do{
            //Obtenemos el nodo de mayor peso
            $currentBulbNode = ['weight'=>0, 'x'=>0, 'y'=>0];
            foreach($analyzedMatrix AS $lIndex => $line){
                foreach($line AS $cIndex => $column){
                    if(!$column['illuminated'] && $column['weight'] >= $currentBulbNode['weight']){
                        $currentBulbNode = ['weight'=>$column['weight'], 'x'=>$cIndex, 'y'=>$lIndex];
                    }
                }
            }
            
            //Ubicamos el nodo y lo marcamos como de tipo foco
            $analyzedMatrix[$currentBulbNode['y']][$currentBulbNode['x']]['isLightBulb'] = true;
            //Marcamos como iluminados los nodos contiguos
            CustomDFSHelper::setIluminatedArea($analyzedMatrix, $currentBulbNode['x'], $currentBulbNode['y']);

            //Obtenemos los nodes ya iluminados hasta este momento
            $illuminated = array_sum(array_map(function($line) { 
                return array_reduce($line, function(&$res, $item) {
                    return $res + ($item['illuminated'] ? 1:0) ;
                }, 0);
            }, $analyzedMatrix));
            $i++;
        }while($illuminated<$totNodes);

        echo \json_encode($analyzedMatrix);
    }
}
