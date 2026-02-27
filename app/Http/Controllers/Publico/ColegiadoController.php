<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Uspdev\Replicado\Pessoa;

class ColegiadoController extends Controller
{
    public function index(){
        
        $colegiados = collect(Pessoa::listarColegiados())->map(function ($item) {
            // convertendo para utf-8
            $fix = fn($str) => mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
    
            return [
                'codclg' => $item['codclg'],
                'sglclg' => $fix($item['sglclg']),
                'tipclg' => $fix($item['tipclg']),
                'nomclg' => $fix($item['nomclg']),
            ];
        });

        return view('colegiados.index',[
            'colegiados' => $colegiados
        ]);

    }
 
    public function show($codclg, $sglclg, Request $request){     
        // Converte a string para UTF-8
        $fix = fn($str) => mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');

        $nomeClg = $fix(Pessoa::retornarNomeColegiado($codclg, $sglclg));

        if(!$nomeClg){
            $request->session()->flash('alert-danger', "Colegiado não encontrado. Busque pelos colegiados listados abaixo.");
            return redirect("/colegiados");
        
        }
        $auxs = Pessoa::listarTitularesSuplentesDoColegiado($codclg, $sglclg);
        
        
        $membros = [];
        foreach($auxs as $aux){
            $aux['nome_titular'] = $fix($aux['nome_titular']);
            $aux['nome_suplente'] = $fix($aux['nome_suplente']);
            $aux['email_titular'] = Pessoa::retornarEmailUsp((int)$aux['titular']) ;
            $aux['email_suplente'] = Pessoa::retornarEmailUsp((int)$aux['suplente']) ;
            $membros[] = $aux;
        }
              
        
        return view('colegiados.show',[
            'sglclg' => $sglclg,
            'codclg' => $codclg,
            'membros' => $membros,
            'nome_colegiado' => $nomeClg,
            
        ]);
    }
}
