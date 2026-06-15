<?php

namespace App\Http\Controllers;

use App\Models\Pacientes; 
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function home()
    {
        
        $pacientes = Pacientes::get(); 

        
        return view('admin.home', compact('pacientes'));
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function users()
    {
        return view('admin.users');
    }
    public function listar()
    {
        return view('admin.listar');
    }
    public function visaogeral()
    {
        return view('admin.visaogeral');
    }
    public function status_totens()
    {
        $totens = Totens::get();
        return view('admin.status_totens', compact('totens'));
    }
    public function pacientes()

    {
        $pacientes = Pacientes::get(); 

        
        return view('admin.pacientes', compact('pacientes'));   
        
    }
    public function alertas()
    {
        return view('admin.alertas');
    }
    public function configuracoes()
    {
        return view('admin.configuracoes');
    }
    public function salvar(Request $request)
    {
        if ($request->id) {
            $p = Pacientes::findOrFail($request->id);
        } else {
            $p = new Pacientes();
        }
        $p->nome = $request->nome;
        $p->cpf = $request->cpf;
        $p-> protocolo = $request->protocolo;
        $p->idade = $request->idade;
        $p->telefone = $request->telefone;
        $p->status = 1;
        $p->data_nascimento = $request->data_nascimento;
        $p->sexo = $request->sexo;
        $p->data_nascimento = $request->data_nascimento;
        $p->sintomas = $request->sintoma;
        $p->tipo_sanguineo = $request->tipo_sanguineo; 
        $p->antecedentes_pessoais = $request->antecedentes_pessoais;
          
        $p->save();
        return redirect("/admin/pacientes");
    }
}