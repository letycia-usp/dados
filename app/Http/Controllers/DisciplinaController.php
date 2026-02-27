<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Uspdev\Replicado\DB;
use App\Utils\ReplicadoTemp;

class DisciplinaController extends Controller
{
    public function turmas(){
        return view('disciplinas.turmas');
    }

    public function prefix($prefix){
        $turmas = ReplicadoTemp::turmas($prefix);
        $turmas = $this->formatarTurmas($turmas);

        return view('disciplinas.turma',[
            'prefix' => $prefix,
            'turmas' => $turmas,
        ]);
    }

    public function concatenate($prefix){
        $turmas = ReplicadoTemp::turmas($prefix);
        $turmas = $this->formatarTurmas($turmas);

        return view('disciplinas.concatenate',[
            'prefix' => $prefix,
            'turmas' => $turmas,
        ]);
    }

    private function formatarTurmas($turmas){
        return collect($turmas)->map(function ($turma) {
            // Função para corrigir os acentos
            $fix = fn($str) => mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');

            $nomdis = \App\Utils\ReplicadoTemp::nomdis($turma['coddis'], $turma['verdis']);
            $ministrantes = \App\Utils\ReplicadoTemp::ministrantes($turma['coddis'], $turma['codtur'], $turma['verdis']);
            $horario = \App\Utils\ReplicadoTemp::horario($turma['coddis'], $turma['codtur'], $turma['verdis']);
    
            return array_merge($turma, [
                'nomdis'        => $fix($nomdis),
                'ministrantes'  => $fix($ministrantes),
                'horario'       => $horario
            ]);
        });

    }
}