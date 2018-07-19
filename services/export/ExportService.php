<?php
/**
 * Created by Madetec-Solution OK.
 * Developer: Mirkhanov Z.S.
 * Date: 19.06.2018
 */

namespace app\services\export;

use app\entities\user\User;
use app\entities\user\UsersGroup;
use app\repositories\dubious\DubiousRepository;
use app\forms\export\CentralBankForm;
use moonland\phpexcel\Excel;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class ExportService
{
    private $dubious;
    private $usersGroup;

    public function __construct(DubiousRepository $dubious, UsersGroup $usersGroup)
    {
        $this->dubious = $dubious;
        $this->usersGroup = $usersGroup;
    }

    public function centralBankCreateFile(CentralBankForm $form)
    {
        if ($request = $this->prepareRequest($form->date)) {
            $dubious = $this->dubious->findByDateDoc($request);

            $criterions = ArrayHelper::getColumn($dubious, 'criterion');
            $users = ArrayHelper::getColumn($dubious, 'created_by');
            $newArray = [];
            foreach ($criterions as $k => $criterion) {
                if (count($newArray) > 0) {
                    $newArray['data'][$users[$k]][0] = $users[$k];
                    $newArray['data'][$users[$k]][1]++;
                    $newArray['data'][$users[$k]][$criterion]++;
                    $newArray['criterions'][$criterion] = $criterion;
                } else {
                    $newArray['data'][$users[$k]][0] = $users[$k];
                    $newArray['data'][$users[$k]][1] = 1;
                    $newArray['data'][$users[$k]][$criterion] = 1;
                    $newArray['criterions'][$criterion] = $criterion;
                }
            }

            foreach ($newArray['data'] as $k => $item) {
                $newArray['data'][$k] = array_values($newArray['data'][$k]);
            }
            $newArray['data'] = array_values($newArray['data']);

            $titles = array_merge(['Наименование филиала банка', 'Кол-во выяв-ных сомнительных операций в филиалах банка'], array_keys($newArray['criterions']));
            unset($newArray['criterions']);

            $file = \Yii::createObject([
                'class' => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Report for Central bank' => [
                        'data' => $newArray['data'],
                        'titles' => $titles,
                        'styles' => [
                            'A1:AO1' => [
                                'alignment' => [
                                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'rotation' => 90,
                                ],
                            ],
                        ]
                    ]
                ]
            ]);
            return $file->send('demo.xlsx');


        }
    }

    private function prepareRequest($date)
    {
        $start = strtotime('first day of this month', strtotime($date));
        $end = strtotime('last day of this month', $start);
        return ['start' => $start, 'end' => $end] ?: false;
    }

}