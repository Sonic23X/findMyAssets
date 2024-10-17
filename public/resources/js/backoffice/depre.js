var url = $('#url').val( );
var downTable = null;

let spanish =
{
  sProcessing: 'Procesando...',
  sLengthMenu: 'Mostrar _MENU_ registros',
  sZeroRecords: 'No se encontraron resultados',
  sEmptyTable: 'Ningún dato disponible en esta tabla',
  sInfo: 'Mostrando _START_ - _END_ de _TOTAL_',
  sInfoEmpty: 'Sin registros',
  sInfoFiltered: '(filtrado de un total de _MAX_ registros)',
  sInfoPostFix: '',
  sSearch: 'Buscar:',
  sUrl: '',
  sInfoThousands: ',',
  sLoadingRecords: 'Cargando...',
  oPaginate:
  {
    sFirst: 'Primero',
    sLast: 'Último',
    sNext: 'Siguiente',
    sPrevious: 'Anterior',
  },
  oAria:
  {
    sSortAscending: ': Activar para ordenar la columna de manera ascendente',
    sSortDescending: ': Activar para ordenar la columna de manera descendente',
  },
};

function imprimir ( titulo, mensaje, tipo )
{
  Swal.fire({
    icon: tipo,
    title: titulo,
    text: mensaje,
    allowOutsideClick: false,
  });
}

function dataURLtoFile( dataurl, filename )
{

   var arr = dataurl.split(','),
       mime = arr[0].match(/:(.*?);/)[1],
       bstr = atob(arr[1]),
       n = bstr.length,
       u8arr = new Uint8Array(n);

   while(n--){
       u8arr[n] = bstr.charCodeAt(n);
   }

   return new File([u8arr], filename, {type:mime});
}

function getInvFormData( )
{
  $( '.iAsignacion' ).html( );
  $( '.iSucursal' ).html( );
  $( '.iEmpresa' ).html( );
  $( '.iTipoActivo' ).html( );
  $( '.iCC' ).html( );

  $.ajax({
    url: url + '/inventario/getFormData',
    type: 'GET',
    dataType: 'json',
  })
  .done( response =>
  {
    if ( response.status == 200 )
    {
      let tipos = response.types;

      tipos.forEach( ( tipo, i ) =>
      {

        let typePlantilla =
        `
          <option value="${ tipo.id }">${ tipo.Desc }</option>
        `;

        $( '.iTipoActivo' ).append( typePlantilla );

      });

      let usuarios = response.users;

      usuarios.forEach( ( usuario , i ) =>
      {

        let typePlantilla =
        `
          <option value="${ usuario.id_usuario }">${ usuario.nombre + ' ' + usuario.apellidos }</option>
        `;

        $( '.iAsignacion' ).append( typePlantilla );

      });

      let empresas = response.empresas;

      empresas.forEach( ( empresa , i ) =>
      {

        let typePlantilla =
        `
          <option value="${ empresa.id_empresa }">${ empresa.nombre }</option>
        `;

        $( '.iEmpresa' ).append( typePlantilla );

      });

      let sucursales = response.sucursales;

      sucursales.forEach( ( sucursal , i ) =>
      {

        let typePlantilla =
        `
          <option value="${ sucursal.id }">${ sucursal.Desc }</option>
        `;

        $( '.iSucursal' ).append( typePlantilla );

      });

      let cc = response.cc;

      cc.forEach( ( ccUnico , i ) =>
      {

        let typePlantilla =
        `
          <option value="${ ccUnico.id }">${ ccUnico.Desc }</option>
        `;

        $( '.iCC' ).append( typePlantilla );

      });

      let depreciaciones = response.depreciacion;

      depreciaciones.forEach( ( depreciacion , i ) =>
      {

        let unidad = depreciacion.Observaciones.split( ' ' );

        let typePlantilla =
        `
          <option value="${ depreciacion.id }">
            ${ depreciacion.Metodo }
          </option>
        `;

        $( '#metodo_depreciacion' ).append( typePlantilla );

      });

      let areas = response.areas;
      
      areas.forEach( ( area , i ) =>
      {

        let typePlantilla =
        `
          <option value="${ area.id }">${ area.descripcion }</option>
        `;

        $( '.iArea' ).append( typePlantilla );

      });

    }
    else
    {
      imprimir( 'Ups..', 'Error al obtener la información del servidor', 'error' );
    }
  });
}

function down( )
{

  let base =
  `
    <thead>
      <tr>
        <th scope="col">Num.</th>
        <th scope="col">Activo</th>
        <th scope="col">Asignación</th>
        <th scope="col">Ultima Act.</th>
        <th></th>
      </tr>
    </thead>
    <tbody class="table-down-actives">

    </tbody>
  `;

  $( '.table-down-actives-content' ).html( base );

  $.ajax({
    url: url + '/depreciacion/getItems',
    type: 'GET',
    dataType: 'json'
  })
  .done( response =>
  {
    if ( response.status == 200 )
    {
      let activos = response.activos;

      $( '.table-down-actives' ).html( '' );
      activos.forEach( ( activo, i ) =>
      {
        let form = activo.metodo != 0 ? true : false;
        
        let typePlantilla =
        `
          <tr>
            <td>
              ${ activo.id_activo }
            </td>
            <td>
              <a class="text-dark text-decoration-none" onClick="viewDownInfo( ${ activo.id } )">
                ${ activo.tipo }
                <br>
                ${ activo.nombre }
              </a>
            </td>
            <td class="align-middle">
              ${ activo.usuario }
            </td>
            <td class="align-middle">
              ${ activo.fecha }
            </td>
            <td>
              <button type="button" class="btn btn-primary btn-sm" name="button" onclick="openModalDepreInfo( ${ form }, ${ activo.id }, ${activo.metodo}, ${activo.vida_util}, ${activo.fecha_depre}, ${activo.precio}, ${activo.unidades} )">
                <i class="fas fa-angle-right"></i>
              </button>
            </td>
          </tr>
        `;

        $( '.table-down-actives' ).append( typePlantilla );

      });

      if ( downTable != null )
        downTable.destroy( );

      //creamos la tabla dinamica
      downTable = $( '.table-down-actives-content' ).DataTable(
      {
        bInfo: false,
        searching: true,
        bLengthChange: false,
        pageLength: 5,
        language: spanish,
      });

      $( '.down-count' ).html( response.number );
    }
    else
    {
      imprimir( 'Ups..', 'Error al obtener la información del servidor', 'error' );
    }
  });
}

function downFiltros( )
{

  let base =
  `
    <thead>
      <tr>
        <th scope="col">Activo</th>
        <th scope="col">Asignación</th>
        <th scope="col">Cargado</th>
        <th></th>
      </tr>
    </thead>
    <tbody class="table-down-actives">

    </tbody>
  `;

  $( '.table-down-actives-content' ).html( base );


  let filtros =
  {
    tipo: $( '#downTipo' ).val( ),
    cc: $( '#downCC' ).val( ),
    empresa: $( '#downEmpresa' ).val( ),
    sucursal: $( '#downSucursal' ).val( ),
    area: $( '#downArea' ).val( ),
  };

  $.ajax({
    url: url + '/bajas/getItemsFilter',
    type: 'POST',
    dataType: 'json',
    data: filtros,
  })
  .done( response =>
  {
    if ( response.status == 200 )
    {
      let activos = response.activos;


      $( '.table-down-actives' ).html( '' );
      activos.forEach( ( activo, i ) =>
      {
        let form = activo.metodo != 0 ? true : false;
        let typePlantilla =
        `
          <tr>
            <td>
              <a class="text-dark text-decoration-none" onClick="viewDownInfo( ${ activo.id } )">
                ${ activo.tipo }
                <br>
                ${ activo.nombre }
              </a>
            </td>
            <td class="align-middle">
              ${ activo.usuario }
            </td>
            <td class="align-middle">
              ${ activo.fecha }
            </td>
            <td>
              <button type="button" class="btn btn-primary btn-sm" name="button" onclick="openModalDepreInfo( ${ form }, ${ activo.id }, ${activo.metodo}, ${activo.vida_util}, ${activo.fecha_depre}, ${activo.precio}, ${activo.unidades} )">
                <i class="fas fa-angle-right"></i>
              </button>
            </td>
          </tr>
        `;

        $( '.table-down-actives' ).append( typePlantilla );

      });

      if ( downTable != null )
        downTable.destroy( );

      //creamos la tabla dinamica
      downTable = $( '.table-down-actives-content' ).DataTable(
      {
        bInfo: false,
        searching: true,
        bLengthChange: false,
        pageLength: 5,
        language: spanish,
      });

      $( '.down-count' ).html( response.number );
    }
    else
    {
      imprimir( 'Ups..', 'Error al obtener la información del servidor', 'error' );
    }
  });
}

function viewDownInfo( id )
{
  $.ajax({
    url: url + `/inventario/getActivoInfo/${ id }`,
    type: 'GET',
    dataType: 'json',
  })
  .done( response =>
  {
    if ( response.status == 200 )
    {
      let activo = response.activo;

      localStorage.setItem( 'process-inventary', activo.ID_Activo );

      $( '#downNoActivo' ).val( activo.ID_Activo );
      $( '#downTipoActivo' ).val( activo.ID_Tipo );
      $( '#downName' ).val( activo.Nom_Activo );
      $( '#downSerie' ).val( activo.NSerie_Activo );
      $( '#downcCosto' ).val( activo.ID_CC );
      $( '#downAsignacion' ).val( activo.User_Inventario );
      $( '#downEmpresa' ).val( activo.ID_Company );
      $( '#downSucursal' ).val( activo.ID_Sucursal );
      $( '#downArea' ).val( activo.ID_Area );
      $( '#downDesc' ).val( activo.Des_Activo );
      $( '#downActualizacion' ).val( activo.TS_Update.split( ' ')[ 0 ] );

      $.ajax({
        url: url + `/activos/getImageFront/${ activo.ID_Activo }`,
        type: 'GET',
        responseType: 'blob',
        contentType: false,
        processData: false,
      })
      .done( response =>
      {
        if ( response != '' )
        {
          $( '.down-image-front' ).html( response );
        }
        else
        {
          $( '.down-image-front' ).html( '<i class="fas fa-5x fa-image"></i>' );
        }
      });

      $.ajax({
        url: url + `/activos/getImageLeft/${ activo.ID_Activo }`,
        type: 'GET',
        contentType: false,
        processData: false,
      })
      .done( response =>
      {
        if ( response != '' )
        {
          $( '.down-image-left' ).html( response );
        }
        else
        {
          $( '.down-image-left' ).html( '<i class="fas fa-5x fa-image"></i>' );
        }
      });

      $.ajax({
        url: url + `/activos/getImageRight/${ activo.ID_Activo }`,
        type: 'GET',
        responseType: 'blob',
        contentType: false,
        processData: false,
      })
      .done( response =>
      {
        if ( response != '' )
        {
          $( '.down-image-right' ).html( response );
        }
        else
        {
          $( '.down-image-right' ).html( '<i class="fas fa-5x fa-image"></i>' );
        }
      });

      $( '#downInfoModal' ).modal( 'show' );
    }

  });
}

function openModalDepreInfo(showForm, activo_id, metodo, vida_util, fecha_depre, precio, unidades = 0) {
    $('#modalContent').empty();    
    localStorage.setItem('activo_id', activo_id);
    
    if (!showForm) {
        // Mostrar el formulario
        $('#modalContent').html($('#formContent').html());
    } else {

        let tableBody = $('#depreciationTableBody');
        tableBody.empty();    
        
        if (metodo == 1) {
            $('#typeDepre').html('Lineal');
            let valorResidual = 0;
            
            let valorDepre = (precio - valorResidual) / vida_util;
            
            for (let i = 0; i < vida_util; i++) {                 
                let row = `<tr>
                            <td>${i + 1}</td>
                            <td>$${(precio - valorResidual).toFixed(2)}</td>
                            <td>$${valorDepre.toFixed(2)}</td>
                            <td>$${(precio - (valorDepre * (i + 1))).toFixed(2)}</td>
                        </tr>`;
                tableBody.append(row);     
                valorResidual += valorDepre;           
            }
        } else if (metodo == 2) {
          $('#typeDepre').html('Acelerada');
          let valorResidual = 0;

          let tasaAcelerada = 2 / vida_util;
          let valorLibros = precio;
          
          for (let i = 0; i < vida_util; i++) {                 
              let valorDepreciacion = valorLibros * tasaAcelerada; // Depreciación del año en curso
          
              if (valorLibros - valorDepreciacion < valorResidual) {
                  valorDepreciacion = valorLibros - valorResidual;
              }
          
              let valorEnLibrosAlFinalDelAno = valorLibros - valorDepreciacion;
          
              let row = `<tr>
                          <td>${i + 1}</td>
                          <td>$${valorLibros.toFixed(2)}</td> <!-- Valor en libros al inicio del año -->
                          <td>$${valorDepreciacion.toFixed(2)}</td> <!-- Depreciación del año -->
                          <td>$${valorEnLibrosAlFinalDelAno.toFixed(2)}</td> <!-- Valor en libros al final del año -->
                        </tr>`;
              
              tableBody.append(row);     
          
              valorLibros = valorEnLibrosAlFinalDelAno;
          
              if (valorLibros <= valorResidual) {
                  break;
              }
          }

        } else if (metodo == 3) {
          $('#typeDepre').html('Unidades de Producción');

          let valorResidual = 0; 
          let unidadesTotales = unidades;
          let unidadesPorAnio = [200, 250, 150, 180, 220]; // Unidades producidas por año

          // Calcular la depreciación por unidad
          let depreciacionPorUnidad = (precio - valorResidual) / unidadesTotales; // Depreciación por cada unidad producida

          // Iterar sobre los años y calcular la depreciación basada en las unidades producidas
          for (let i = 0; i < unidadesPorAnio.length; i++) {
              let unidadesProducidas = unidadesPorAnio[i]; // Unidades producidas en el año i
              let depreciacionAnual = depreciacionPorUnidad * unidadesProducidas; // Depreciación para el año en función de las unidades producidas
              let valorLibrosAlInicio = precio - (depreciacionPorUnidad * unidadesPorAnio.slice(0, i).reduce((acc, val) => acc + val, 0)); // Valor en libros al inicio del año

              // Crear la fila de la tabla
              let row = `<tr>
                          <td>${i + 1}</td> <!-- Año -->
                          <td>$${valorLibrosAlInicio.toFixed(2)}</td> <!-- Valor en libros al inicio del año -->
                          <td>$${depreciacionAnual.toFixed(2)}</td> <!-- Depreciación del año -->
                          <td>$${(valorLibrosAlInicio - depreciacionAnual).toFixed(2)}</td> <!-- Valor en libros al final del año -->
                        </tr>`;

              // Agregar la fila a la tabla
              tableBody.append(row);
          }
        }

        // Mostrar la tabla
        $('#modalContent').html($('#tableContent').html());
    }
  
    // Mostramos el modal
    $('#showDepreModal').modal('show');
}

function calcularDiferenciaMeses(fecha) {
    const hoy = new Date(); // Fecha actual
    const fechaDada = new Date(fecha); // Convertir la fecha dada a un objeto Date
  
    // Calcular la diferencia en años y meses
    const anios = hoy.getFullYear() - fechaDada.getFullYear();
    const meses = hoy.getMonth() - fechaDada.getMonth();
  
    // Calcular la diferencia total en meses
    let diferenciaTotalMeses = anios * 12 + meses;
  
    // Ajuste si el día del mes de la fecha dada es mayor que el de hoy (para evitar contar el mes incompleto)
    if (hoy.getDate() < fechaDada.getDate()) {
      diferenciaTotalMeses--;
    }
  
    return diferenciaTotalMeses;
}

function saveDepre() {

    //Get the values
    let activo_id = localStorage.getItem('activo_id');
    let metodo = $('#metodo_depreciacion').val();
    let vida_util = $('#vidautilnew').val();
    let fecha = $('#fechastart').val();

    if (vida_util == '' || fecha == '') {
        imprimir('Ups...', 'Por favor complete todos los campos', 'error');
        return;
    }

    let json = {
        activo_id: activo_id,
        metodo: metodo,
        vida_util: vida_util,
        fecha: fecha
    };

    //subir a servidor
    $.ajax({
        url: url + '/depreciacion/saveDepre',
        type: 'POST',
        dataType: 'json',
        data: json,
    }).done(response => {
        if (response.status == 200) {
            imprimir('¡Hecho!', response.msg, 'success');
            $('#showDepreModal').modal('hide');
            down();
        } else {
            imprimir('Ups...', response.msg, 'error');
        }
    }).fail(() => {
        imprimir('Ups...', 'Error al conectar con el servidor, intente más tarde', 'error');
    });
}

function viewForm() {
  if ($('#metodo_depreciacion').val() == 3) {
    $('#formUnidadesProducidas').removeClass('d-none');
  } else {
    $('#formUnidadesProducidas').addClass('d-none');
  }
}

$(document).ready(function( )
{
    getInvFormData( );
    down( );

    $( '#down-select' ).change( (event) =>
    {
        $( '.motivo-down-form' ).removeClass( 'd-none' );
    });

    $( '#downEmpresa' ).change( event =>
    {
        let data = 
        {
            empresa: $( '#downEmpresa' ).val( ),
        };

        //buscamos el codigo en la BDD
        $.ajax({
            url: url + '/inventario/sucursales',
            type: 'POST',
            dataType: 'json',
            data: data
        })
        .done( response =>
        {
            if (response.status == 200)
            {
                $( '#downSucursal' ).html( '' );
                $( '#downSucursal' ).append( '<option value="">Todas</option>' );
                $( '#downArea' ).html( '' );
                $( '#downArea' ).append( '<option value="">Todas</option>' );

                response.sucursales.forEach( ( sucursal , i ) =>
                {

                    let typePlantilla =
                    `
                        <option value="${ sucursal.id }">${ sucursal.Desc }</option>
                    `;

                    $( '#downSucursal' ).append( typePlantilla );

                });

                response.areas.forEach( ( area , i ) =>
                {

                    let typePlantilla =
                    `
                        <option value="${ area.id }">${ area.descripcion }</option>
                    `;

                    $( '#downArea' ).append( typePlantilla );

                });
            }
            else
            {
                imprimir( 'Ups..', response.msg, 'error' );
            }
        });

    });

    $('#combo-empresas').change( event => 
    {
      let json = 
      {
        id: $('#combo-empresas').val(),
      };

      //subir a servidor
      $.ajax({
        url: url + '/empresas/changeCompany',
        type: 'POST',
        dataType: 'json',
        data: json,
      })
      .done( response =>
      {
        if ( response.status == 200 )
        {
          Swal.fire({
            icon: 'success',
            title: '¡Hecho!',
            text: response.msg,
            allowOutsideClick: false,
          })
          .then((result) => {
            if (result.isConfirmed) 
              location.reload();
          });
        }
        else
          imprimir( 'Ups...', response.msg, 'error' );
      })
      .fail( ( ) =>
      {
        imprimir( 'Ups...', 'Error al conectar con el servidor, intente más tarde', 'error' );
      });
    });

    $('#metodo_depreciacion').change( event => {

      //Get the values
      console.log(event);
      

    });

}); 