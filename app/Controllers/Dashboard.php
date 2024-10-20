<?php

namespace App\Controllers;

use App\Libraries\PHPMailerLib;

class Dashboard extends BaseController
{

	//variables de la clase
	protected $session;
	protected $tipoModel;
	protected $activoModel;
	protected $ccModel;
	protected $db;

	function __construct()
	{
		$this->session = \Config\Services::session( );
		$this->tipoModel = model( 'App\Models\TipoModel' );
		$this->activoModel = model( 'App\Models\ActivoModel' );
		$this->ccModel = model( 'App\Models\CCModel' );
		$this->db = \Config\Database::connect();
	}

	function Index( )
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

			//content - inicio
			echo view( 'backoffice/sections/start' );

			//Scripts y librerias
			$footer = array( 'js' => 'dash', 'dashboard' => true, 'carga' => false, 'inv' => false, 'bajas' => false );
			echo view( 'backoffice/common/footer', $footer );
		}
		else
		{
			$data = array( 'url' => base_url( '/ingreso' ) );
			return view( 'functions/redirect', $data );
		}
	}

	function getData( )
	{
		$tipos = $this->tipoModel->where( 'ID_Empresa', $this->session->empresa )->findAll( );
		
		$table1 = [ ];
		$labels = [ ];
		$values = [ ];
		foreach( $tipos as $tipo )
		{
			$activos = $this->activoModel->where( 'ID_Tipo', $tipo['id'] )
										 ->where( 'TS_Delete', null )
										 ->select( 'Pre_Compra' )
										 ->findAll( );
			$monto = 0;
			$num = 0;
			foreach( $activos as $activo )
			{
				$monto = $monto + $activo[ 'Pre_Compra' ];
				$num++;
			}

			array_push( $table1, [ 'tipo' => $tipo[ 'Desc' ], 'monto' => $monto ] );
			array_push( $labels, $tipo[ 'Desc' ] );
			array_push( $values, $num );
		}

		$SQL = "SELECT * FROM empresa_periodo WHERE id_empresa = " . $this->session->empresa . " AND status = 1";
        $builderPeriodo = $this->db->query( $SQL );
        $periodo = $builderPeriodo->getResult( );

		$builder = $this->db->table( 'activos' );
        $builder->select( 'activos.Id, activos.Nom_Activo, activos.ID_Activo, activos.Fec_Inventario, activos.TS_Update, tipos.Desc, usuarios.nombre, usuarios.apellidos' );
        $builder->join( 'tipos', 'tipos.id = activos.ID_Tipo' );
        $builder->join( 'usuarios', 'usuarios.id_usuario = activos.User_Inventario' );
        $builder->where( 'activos.ID_Company', $this->session->empresa );
        $builder->where( 'activos.TS_Delete', null );
        $activosTotal = $builder->get()->getResult();

		$activosTotales = 0;
		$inv = 0;

		$statusPeriodo = 'Sin periodo de inventario en curso';
		if ($periodo != null)
		{
			$fechaInicio = explode('-', $periodo[0]->fecha_inicio);
			$fechaFin = explode('-', $periodo[0]->fecha_fin);

			$date1 = new \DateTime('NOW');
			$date2 = new \DateTime($periodo[0]->fecha_fin);
			$diff = $date1->diff($date2);

			$fecha_actual = strtotime(date("d-m-Y"));
			$fechaFinUnix = strtotime($fechaFin[2]."-".$fechaFin[1]."-".$fechaFin[0]." 00:00:00");
			if($fecha_actual > $fechaFinUnix)
				$statusPeriodo = 'El periodo de inventario finalizó hace <b>' . $diff->days . ' días</b>';
			else
				$statusPeriodo = 'Quedan <b>' . $diff->days . ' días</b> del periodo de inventario';
		}

		foreach ($activosTotal as $row) 
		{
			$inventario = false;
			if ($periodo != null && $row->Fec_Inventario != null) 
			{
				$fecha1 = explode('-', explode(' ', $row->Fec_Inventario)[0]);
				$fechaInicio = explode('-', $periodo[0]->fecha_inicio);
				$fechaFin = explode('-', $periodo[0]->fecha_fin);

				$fecha1Unix = strtotime($fecha1[2]."-".$fecha1[1]."-".$fecha1[0]." 00:00:00");
				$fechaInicioUnix = strtotime($fechaInicio[2]."-".$fechaInicio[1]."-".$fechaInicio[0]." 00:00:00");
				$fechaFinUnix = strtotime($fechaFin[2]."-".$fechaFin[1]."-".$fechaFin[0]." 00:00:00");
				
				if($fecha1Unix >= $fechaInicioUnix && $fecha1Unix <= $fechaFinUnix)
					$inv++;
			}
			else
				$activosTotales++;
		}
		

		$bajas = $this->activoModel->where( 'ID_Company', $this->session->empresa )->where( 'TS_Delete !=', null )
																				   ->select( 'TS_Delete, Nom_Activo, Pre_Compra' )
																				   ->orderBy('TS_Create', 'desc')
																				   ->findAll( 5 );

		$altas = $this->activoModel->where( 'ID_Company', $this->session->empresa )->where( 'TS_Delete', null )
																				   ->select( 'TS_Create, Nom_Activo, , Pre_Compra' )
																				   ->orderBy('TS_Create', 'desc')
																				   ->findAll( 5 );

		$builder = $this->db->table( 'activos' );
        $builder->select( 'activos.Nom_Activo, activos.GPS, tipos.Desc, usuarios.nombre' );
        $builder->join( 'tipos', 'tipos.id = activos.ID_Tipo' );
		$builder->join( 'usuarios', 'usuarios.id_usuario = activos.User_Inventario' );
		$builder->where( 'activos.ID_Company', $this->session->empresa );
		$builder->where( 'activos.Fec_Inventario !=', null );
        $builder->where( 'activos.TS_Delete', null );
        $points = $builder->get( 10 )->getResult( );

		echo json_encode( 
		[
			'status' => 200, 
			'montos' => $table1, 
			'graficaLabels' => $labels, 
			'graficaValues' => $values, 
			'bajas' => $bajas, 
			'altas' => $altas, 
			'points' => $points, 
			'inventariados' => $inv, 
			'activos' => $activosTotales, 
			'periodo' => $statusPeriodo,
			'tipos' => $this->tipoModel->where('ID_Empresa', $this->session->empresa)->findAll(),
			'cc' => $this->ccModel->where('id_empresa', $this->session->empresa)->findAll(),
		]);
	}

	function getActivosMap( )
	{
		if ( $this->request->isAJAX( ) )
        {	
			$builder = $this->db->table( 'activos' );
			$builder->select( 'activos.Nom_Activo, activos.GPS, tipos.Desc, usuarios.nombre' );
			$builder->join( 'tipos', 'tipos.id = activos.ID_Tipo' );
			$builder->join( 'usuarios', 'usuarios.id_usuario = activos.User_Inventario' );
			$builder->where( 'activos.Fec_Inventario !=', null );
			$builder->where( 'activos.ID_Company', $this->session->empresa );
			$builder->where( 'activos.TS_Delete', null );
			
			if ( $this->request->getVar( 'tipo' ) != null && $this->request->getVar( 'tipo' ) != '0' )
			{
				$builder->where( 'activos.ID_Tipo', $this->request->getVar( 'tipo' ) );
			}
			if ( $this->request->getVar( 'cc' ) != null && $this->request->getVar( 'cc' ) != '0' )
			{
				$builder->where( 'activos.ID_CC', $this->request->getVar( 'cc' ) );
			}
			if ( $this->request->getVar( 'busqueda' ) != null && $this->request->getVar( 'busqueda' ) != '' )
			{
				$builder->like( 'activos.Nom_Activo', $this->request->getVar( 'busqueda' ) );
			}
			
			if ( $this->request->getVar( 'cantidad' ) != '' )
				$builder->limit($this->request->getVar( 'cantidad' ));
			
			$points = $builder->get( )->getResult( );

			echo json_encode( 
			[
				'status' => 200,
				'points' => $points, 
			]);
        }
        else
            return view( 'errors/cli/error_404' );
	}

	function getDataFilter( )
	{
		if ( $this->request->isAJAX( ) )
        {
			$tipos = $this->tipoModel->where( 'ID_Empresa', $this->session->empresa )->findAll( );
		
			$labels = [ ];
			$values = [ ];
			foreach( $tipos as $tipo )
			{
				$builder = $this->db->table( 'activos' );
				$builder->select( 'Pre_Compra' );
				$builder->where( 'activos.ID_Company', $this->session->empresa );
				$builder->where( 'activos.ID_Tipo', $tipo['id'] );
				if ($this->request->getVar('cc') != '' && $this->request->getVar('cc') != '0') 
					$builder->where( 'activos.ID_CC', $this->request->getVar('cc'));
				$builder->where( 'activos.TS_Delete', null );
				$activos = $builder->get()->getResult();

				$monto = 0;
				$num = 0;
				foreach( $activos as $activo )
				{
					$monto = $monto + $activo->Pre_Compra;
					$num++;
				}

				array_push( $labels, $tipo[ 'Desc' ] );
				array_push( $values, $num );
			}

			$SQL = "SELECT * FROM empresa_periodo WHERE id_empresa = " . $this->session->empresa . " AND status = 1";
			$builderPeriodo = $this->db->query( $SQL );
			$periodo = $builderPeriodo->getResult( );

			$builder = $this->db->table( 'activos' );
			$builder->select( 'activos.Id, activos.Nom_Activo, activos.ID_Activo, activos.Fec_Inventario, activos.TS_Update, tipos.Desc, usuarios.nombre, usuarios.apellidos' );
			$builder->join( 'tipos', 'tipos.id = activos.ID_Tipo' );
			$builder->join( 'usuarios', 'usuarios.id_usuario = activos.User_Inventario' );

			if ($this->request->getVar('cc') != '' && $this->request->getVar('cc') != '0') 
				$builder->where( 'activos.ID_CC', $this->request->getVar('cc'));

			if ($this->request->getVar('tipo') != '' && $this->request->getVar('tipo') != '0') 
				$builder->where( 'activos.ID_Tipo', $this->request->getVar('tipo'));

			$builder->where( 'activos.ID_Company', $this->session->empresa );
			$builder->where( 'activos.TS_Delete', null );
			$activosTotal = $builder->get()->getResult();

			$activosTotales = 0;
			$inv = 0;

			$statusPeriodo = 'Sin periodo de inventario en curso';
			if ($periodo != null)
			{
				$fechaInicio = explode('-', $periodo[0]->fecha_inicio);
				$fechaFin = explode('-', $periodo[0]->fecha_fin);

				$date1 = new \DateTime('NOW');
				$date2 = new \DateTime($periodo[0]->fecha_fin);
				$diff = $date1->diff($date2);

				$fecha_actual = strtotime(date("d-m-Y"));
				$fechaFinUnix = strtotime($fechaFin[2]."-".$fechaFin[1]."-".$fechaFin[0]." 00:00:00");
				if($fecha_actual > $fechaFinUnix)
					$statusPeriodo = 'El periodo de inventario finalizó hace <b>' . $diff->days . ' días</b>';
				else
					$statusPeriodo = 'Quedan <b>' . $diff->days . ' días</b> del periodo de inventario';
			}

			foreach ($activosTotal as $row) 
			{
				$inventario = false;
				if ($periodo != null && $row->Fec_Inventario != null) 
				{
					$fecha1 = explode('-', explode(' ', $row->Fec_Inventario)[0]);
					$fechaInicio = explode('-', $periodo[0]->fecha_inicio);
					$fechaFin = explode('-', $periodo[0]->fecha_fin);

					$fecha1Unix = strtotime($fecha1[2]."-".$fecha1[1]."-".$fecha1[0]." 00:00:00");
					$fechaInicioUnix = strtotime($fechaInicio[2]."-".$fechaInicio[1]."-".$fechaInicio[0]." 00:00:00");
					$fechaFinUnix = strtotime($fechaFin[2]."-".$fechaFin[1]."-".$fechaFin[0]." 00:00:00");
					
					if($fecha1Unix >= $fechaInicioUnix && $fecha1Unix <= $fechaFinUnix)
						$inv++;
				}
				else
					$activosTotales++;
			}

			$builder = $this->db->table( 'activos' );
			$builder->select( 'TS_Delete, Nom_Activo, Pre_Compra' );
			$builder->orderBy('TS_Create', 'desc');
			
			if ($this->request->getVar('cc') != '' && $this->request->getVar('cc') != '0') 
				$builder->where( 'activos.ID_CC', $this->request->getVar('cc'));

			if ($this->request->getVar('tipo') != '' && $this->request->getVar('tipo') != '0') 
				$builder->where( 'activos.ID_Tipo', $this->request->getVar('tipo'));

			$builder->where( 'activos.ID_Company', $this->session->empresa );
			$builder->where( 'activos.TS_Delete !=', null );
			$bajas = $builder->get( 5 )->getResult( );

			$builder = $this->db->table( 'activos' );
			$builder->select( 'TS_Create, Nom_Activo, , Pre_Compra' );
			$builder->orderBy('TS_Create', 'desc');
			
			if ($this->request->getVar('cc') != '' && $this->request->getVar('cc') != '0') 
				$builder->where( 'activos.ID_CC', $this->request->getVar('cc'));

			if ($this->request->getVar('tipo') != '' && $this->request->getVar('tipo') != '0') 
				$builder->where( 'activos.ID_Tipo', $this->request->getVar('tipo'));

			$builder->where( 'activos.ID_Company', $this->session->empresa );
			$builder->where( 'activos.TS_Delete', null );
			$altas = $builder->get( 5 )->getResult( );
			
			$builder = $this->db->table( 'activos' );
			$builder->select( 'activos.Nom_Activo, activos.GPS, tipos.Desc, usuarios.nombre' );
			$builder->join( 'tipos', 'tipos.id = activos.ID_Tipo' );
			$builder->join( 'usuarios', 'usuarios.id_usuario = activos.User_Inventario' );
			
			if ($this->request->getVar('cc') != '' && $this->request->getVar('cc') != '0') 
				$builder->where( 'activos.ID_CC', $this->request->getVar('cc'));

			if ($this->request->getVar('tipo') != '' && $this->request->getVar('tipo') != '0') 
				$builder->where( 'activos.ID_Tipo', $this->request->getVar('tipo'));
			
			$builder->where( 'activos.Fec_Inventario !=', null );
			$builder->where( 'activos.ID_Company', $this->session->empresa );
			$builder->where( 'activos.TS_Delete', null );

			$points = $builder->get( 10 )->getResult( );

			echo json_encode( 
			[
				'bajas' => $bajas, 
				'altas' => $altas, 
				'status' => 200,
				'points' => $points, 
				'graficaLabels' => $labels, 
				'graficaValues' => $values, 
				'inventariados' => $inv, 
				'activos' => $activosTotales, 
				'periodo' => $statusPeriodo,
			]);
		}
		else
            return view( 'errors/cli/error_404' );
	}
}
