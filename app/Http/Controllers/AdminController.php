<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function home()
    {
        return view('admin.home');
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
        return view('admin.status_totens');
    }
    public function pacientes()
    {
        return view('admin.pacientes');
    }
    public function alertas()
    {
        return view('admin.alertas');
    }
    public function configuracoes()
    {
        return view('admin.configuracoes');
    }
}