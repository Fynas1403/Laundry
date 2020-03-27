<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Transaksi_model;
use App\Detail_model;
use App\JenisCuci_model;
use Auth;
use DB;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'id_pelanggan'=>'required',
            'id_petugas'=>'required',
            'tgl_awal'=>'required',
            'tgl_akhir'=>'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),
            400);
        }else{
            $insert=Transaksi_model::insert([
                'id_pelanggan'=>$request->id_pelanggan,
                'id_petugas'=>$request->id_petugas,
                'tgl_awal'=>$request->tgl_awal,
                'tgl_akhir'=>$request->tgl_akhir
            ]);
            if($insert){
                $status="Sukses menambahkan data!";
            }else{
                $status="Gagal menambahkan data!";
            }
            return response()->json(compact('status'));
        }
    }

    public function tampil_transaksi(Request $req)
    {
        $transaksi=DB::table('transaksi')->join('pelanggan', 'pelanggan.id', 'transaksi.id_pelanggan')
                                         ->where('transaksi.tgl_awal','>=',$req->tgl_awal)
                                         ->where('transaksi.tgl_awal','<=',$req->tgl_akhir)
                                         ->select('nama_pelanggan','telp','alamat','transaksi.id','tgl_awal','tgl_akhir')
                                         ->get();

        if($transaksi->count() > 0){
            $data_transaksi = array();
       

        foreach ($transaksi as $t){
            $grand = DB::table('detail_transaksi')->where('id_transaksi','=',$t->id) 
            ->groupBy('id_transaksi')
            ->select(DB::raw('sum(subtotal) as grandtotal'))
            ->first();
            
            $detail = DB::table('detail_transaksi')->join('jenis_cuci','detail_transaksi.id_jenis','=','jenis_cuci.id')
            ->where('id_transaksi','=',$t->id)
            ->get();

            $data_transaksi = array(
                'tgl' => $t->tgl_awal,
                'nama pelanggan' => $t->nama_pelanggan,
                'alamat' => $t->alamat,
                'telp' => $t->telp,
                'tanggal ambil' => $t->tgl_akhir,
                'grand total' => $grand,
                'detail' => $detail,
            );
        }
        return Response()->json($data_transaksi);
    }else{
        $status = 'tidak ada transaksi antara tanggal '.$req->tgl_awal.' sampai dengan tanggal'.$req->tgl_akhir;
        return response()->json(compact('status'));
    }
    }
}
