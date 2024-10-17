<?php

namespace App\Controllers;

class Depreciacion extends BaseController
{
    //variables de la clase
    protected $session;
    protected $sucursalModel;
    protected $activoModel;
    protected $areaModel;
    protected $db;

    function __construct()
    {
        $this->session = \Config\Services::session( );
        $this->sucursalModel = model( 'App\Models\SucursalModel' );
        $this->activoModel = model( 'App\Models\ActivoModel' );
        $this->areaModel = model( 'App\Models\AreaModel' );
        $this->db = \Config\Database::connect();
    }

    public function Index( )
    {
        if ( $this->session->has( 'isLoggin' ) && $this->session->has( 'tipo' ) && ($this->session->tipo == 'admin' || $this->session->tipo == 'superadmin'))
		{
			//CSS, METAS y titulo
			$head = array( 'title' => 'Dashboard | Find my assets', 'css' => 'dashboard' );
			echo view( 'backoffice/common/head', $head );

			//sidebar
			$SQL = "SELECT empresas.id_empresa, empresas.nombre FROM empresas, user_empresa WHERE user_empresa.id_empresa = empresas.id_empresa AND user_empresa.id_usuario = " . $this->session->id;
			$builder = $this->db->query( $SQL );
			$empresas = $builder->getResult( );
			$sidebar = array( 'name' => $this->session->name, 'empresas' => $empresas, 'actual' => $this->session->empresa);
			echo view( 'backoffice/common/sidebar', $sidebar );

			//navbar
			echo view( 'backoffice/common/navbar' );

			//content - bajas
			echo view( 'backoffice/sections/contable' );

			//Scripts y librerias
			$footer = array( 'js' => 'depre', 'dashboard' => false, 'carga' => false, 'inv' => false, 'bajas' => true );
			echo view( 'backoffice/common/footer', $footer );
		}
		else
		{
			$data = array( 'url' => base_url( '/ingreso' ) );
			return view( 'functions/redirect', $data );
		}
    }

    //método que funciona exclusivamente con AJAX - JQUERY
    function SearchList( )
    {
        if ( $this->request->isAJAX( ) )
        {
            try
            {
                $builder = $this->db->table( 'activos' );
                $builder->select( 'activos.Id, activos.Nom_Activo, activos.ID_Activo, activos.TS_Update, activos.ID_MetDepre, activos.Vida_Activo, activos.Fec_Compra, activos.Pre_Compra, activos.unidadesProducidas, tipos.Desc, usuarios.nombre, usuarios.apellidos' );
                $builder->join( 'tipos', 'tipos.id = activos.ID_Tipo' );
                $builder->join( 'usuarios', 'usuarios.id_usuario = activos.User_Inventario' );
                $builder->where( 'activos.TS_Delete', null );
                $builder->where( 'activos.ID_Company', $this->session->empresa );

                $activos = $builder->get( );

                if ( $activos == null )
                {
                    echo json_encode( array( 'status' => 400, 'msg' => 'Activos no encontrados' ) );
                    return;
                }

                $data = [ ];
                $num = 0;
                foreach ( $activos->getResult( ) as $row )
                {
                    $fecha = explode( ' ', $row->TS_Update );

                    $json =
                    [
                        'id' => $row->Id,
                        'tipo' => $row->Desc,
                        'nombre' => $row->Nom_Activo,
                        'usuario' => $row->nombre . ' ' . $row->apellidos,
                        'fecha' => $fecha[ 0 ],
                        'id_activo' => $row->ID_Activo,
                        'metodo' => $row->ID_MetDepre,
                        'vida_util' => $row->Vida_Activo,
                        'fecha_depre' => $row->Fec_Compra,
                        'precio' => $row->Pre_Compra,
                        'unidades' => $row->unidadesProducidas
                    ];

                    array_push( $data, $json );
                    $num++;
                }

                echo json_encode( array( 'status' => 200, 'activos' => $data, 'number' => $num ) );
            }
            catch (\Exception $e)
            {
                echo json_encode( array( 'status' => 400, 'msg' => $e->getMessage( ) ) );
            }
        }
        else
        return view( 'errors/cli/error_404' );
    }

    //método que funciona exclusivamente con AJAX - JQUERY
    function saveDepre( )
    {
        if ( $this->request->isAJAX( ) )
        {
            try
            {
              $update =
              [
                'ID_MetDepre' => $this->request->getVar( 'metodo' ),
                'Fec_InicioDepre' => $this->request->getVar( 'fecha' ),
                'Vida_Activo' => $this->request->getVar( 'vida_util' ),
                'unidadesProducidas' => $this->request->getVar( 'unidades' ) ?? 0,
                'contabilizar' => 1,
                'TS_Update' => date( 'Y/n/j' ),
                'status' => 'editado',
              ];
      
              if ( $this->activoModel->where( 'Id', $this->request->getVar( 'activo_id' ) )->where('ID_Company', $this->session->empresa)->set( $update )->update( ) )
                echo json_encode( array( 'status' => 200 ) );
              else
                echo json_encode( array( 'status' => 400, 'msg' => 'Error al actualizar el activo. Intente más tarde' ) );
            }
            catch (\Exception $e)
            {
              echo json_encode( array( 'status' => 400, 'msg' => $e->getMessage( ) ) );
            }
        }
        else
        return view( 'errors/cli/error_404' );
    }
}