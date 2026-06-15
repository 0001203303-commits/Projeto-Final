<?php
namespace App\Http\Controllers;
use App\Models\Totens; 
use Illuminate\Http\Request;

class TotemController extends Controller
{
    public function deletarTotem($id)
    {
        
        $t = Totens::findOrFail($id);
         if ($t) {
        $t->delete();
         return redirect("/admin/status_totens");    
         } else {
            return redirect("/admin/status_totens")->with('error', 'Totem não encontrado.');
            }
    }
    public function salvarTotem(Request $request)
    {
        if ($request->id) {
            $t = Totens::findOrFail($request->id);
        } else {
            $t = new Totens();
        }
        $t->nome = $request->nome;
        $t->status = $request->status;
        $t->save();
        return redirect("/admin/status_totens");
}
    public function status_totens()
    {
       
        $totens = Totens::get();
        return view('admin.status_totens', compact('totens'));
    }
}