
            <div class="depre">

              <div class="row text-center">
                <div class="col-12 col-sm-12 col-md-12 text-center title-downs">
                  <b>Evalua el tema contable de tus activos</b>
                </div>
              </div>

              <div class="row mt-3 p-2 instructions text-center">
                <div class="col-12 col-sm-12 text-center">
                  <span id="down-instructions">Selecciona alguno de tus activos y establece/consulta su estatus contable</span>
                </div>
              </div>

              <div class="card">
                <div class="card-body">

                  <div class="row mb-3">
                    <div class="col-6 float-left align-middle">
                      <span>Total de activos: <b class="down-count">XX</b> </span>
                    </div>
                    <div class="col-6 float-right">

                    </div>
                  </div>

                  <div class="card collapsed-card">
                    <div class="card-header text-center filtros-card-background">
                      <span class="text-white">Filtros</span>
                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" style="color: white">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="text-center">
                        <div class="form-group row">
                          <label for="tipoActivo" class="col-sm-2 col-form-label">Tipo de activo</label>
                          <div class="col-sm-10">
                            <select class="custom-select iTipoActivo" id="downTipo" name="tipoActivo" onchange="downFiltros( )">
                              <option value="">Todos</option>
                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="cCosto" class="col-sm-2 col-form-label">Centro de costo</label>
                          <div class="col-sm-10">
                            <select class="custom-select iCC" id="downFCC" name="cCosto" onchange="downFiltros( )">
                              <option value="">Todas</option>
                            </select>
                          </div>
                        </div>


                        <div class="row">
                          <div class="col-12 text-center">
                            <span><b>Ubicación</b></span>
                          </div>
                        </div>

                        <div class="form-group row d-none">
                          <label for="asignacion" class="col-sm-2 col-form-label">Empresa</label>
                          <div class="col-sm-10">
                            <select class="custom-select iEmpresa" id="downEmpresa" name="asignacion" onchange="downFiltros( )">
                              <option value="">Todos</option>
                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="asignacion" class="col-sm-2 col-form-label">Sucursal</label>
                          <div class="col-sm-10">
                            <select class="custom-select iSucursal" id="downSucursal" name="asignacion" onchange="downFiltros( )">
                              <option value="">Todas</option>
                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="asignacion" class="col-sm-2 col-form-label">Área</label>
                          <div class="col-sm-10">
                            <select class="custom-select iArea" id="downArea" name="asignacion" onchange="downFiltros( )">
                              <option value="">Todas</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-2 mb-2 delete-button-down d-none">
                    <button type="button" class="btn btn-sm btn-danger btn-block" onclick="multipleDelete( )">Eliminar</button>
                  </div>

                  <div class="mt-3 table-responsive text-center">
                    <table class="table table-hover table-down-actives-content">
                      <thead>
                        <tr>
                          <th scope="col">Activo</th>
                          <th scope="col">Asignación</th>
                          <th scope="col">Detalles</th>
                        </tr>
                      </thead>
                      <tbody class="table-down-actives">

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="modal fade" id="downInfoModal" tabindex="-1" role="dialog" aria-labelledby="downInfoModallLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="downInfoModalLabel">Detalles del activo</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form class="active-inventary-form">

                        <div class="form-group row">
                          <label for="name" class="col-sm-4 col-form-label">Numero de activo</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control-plaintext" id="downNoActivo" placeholder="Numero de activo" disabled>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="tipoActivo" class="col-sm-4 col-form-label">Tipo de activo</label>
                          <div class="col-sm-8">
                            <select class="form-control-plaintext iTipoActivo" name="tipoActivo" id="downTipoActivo" disabled>

                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="name" class="col-sm-4 col-form-label">Nombre</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control-plaintext" id="downName" placeholder="Ej. Macbook PRO" disabled>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="serie" class="col-sm-4 col-form-label">
                            No. de serie
                            <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="right"
                                    style="border-radius: 25px; font-size: 9px !important;"
                                    title="Campo actualizado, valor anterior: MXN56231">
                              <i class="fas fa-info"></i>
                            </button>
                          </label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control-plaintext" id="downSerie" placeholder="Ej. Nombre" disabled>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="cCosto" class="col-sm-4 col-form-label">Centro de costo</label>
                          <div class="col-sm-8">
                            <select class="form-control-plaintext iCC" name="cCosto" id="downcCosto" disabled>
                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="asignacion" class="col-sm-4 col-form-label">Asignado a</label>
                          <div class="col-sm-8">
                            <select class="form-control-plaintext iAsignacion" name="asignacion" id="downAsignacion" disabled>

                            </select>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-12 text-center">
                            <span><b>Ubicación</b></span>
                          </div>
                        </div>

                        <div class="form-group row d-none">
                          <label for="asignacion" class="col-sm-4 col-form-label">Empresa</label>
                          <div class="col-sm-8">
                            <select class="form-control-plaintext iEmpresa" name="asignacion" id="downEmpresa" disabled>

                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="asignacion" class="col-sm-4 col-form-label">Sucursal</label>
                          <div class="col-sm-8">
                            <select class="form-control-plaintext iSucursal" name="asignacion" id="downSucursal" disabled>

                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="asignacion" class="col-sm-4 col-form-label">Área</label>
                          <div class="col-sm-8">
                            <select class="form-control-plaintext" name="asignacion" id="downArea" disabled>

                            </select>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="desc" class="col-sm-4 col-form-label">Ultima actualización</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control-plaintext" id="downActualizacion" disabled>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label for="desc" class="col-sm-4 col-form-label">Descripción</label>
                          <div class="col-sm-8">
                            <textarea class="form-control-plaintext" id="downDesc" rows="3" disabled></textarea>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal fade" id="showDepreModal" tabindex="-1" role="dialog" aria-labelledby="downInfoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="downInfoModalLabel">Depreciación</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div id="modalContent"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contenido del formulario -->
            <div id="formContent" style="display:none;">
              <form>
                <div class="form-group row">
                  <label for="medotoD" class="col-sm-2 col-form-label">Metodo de depreciacion</label>
                  <div class="col-sm-10">
                    <select class="custom-select" name="medotoD" id="metodo_depreciacion" onchange="viewForm()">

                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="name" class="col-sm-2 col-form-label">Fecha de inicio</label>
                  <div class="col-sm-10">
                    <input id="fechastart" type="date" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label for="name" class="col-sm-2 col-form-label">Vida útil</label>
                  <div class="col-sm-10">
                    <input id="vidautilnew" type="number" class="form-control">
                  </div>
                </div>

                <div class="form-group row d-none" id="formUnidadesProducidas">
                  <label for="unidadesTotales" class="col-sm-2 col-form-label">Unidades totales estimadas</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" id="unidadesTotales" value="0">
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-8">
                    Componentes
                  </div>
                  <div class="col-4 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm" name="button">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </div>


                <div class="form-group row">
                  <button type="button" class="btn btn-primary btn-block" onclick="saveDepre( )">Guardar</button>
                </div>
              </form>
            </div>

            <!-- Contenido de la tabla -->
            <div id="tableContent" style="display:none;">
              <div class="mb-3">
                Tipo de depreciacion: <b id="typeDepre">###</b>
              </div>
              <div>
                Tabla de depreciación
              </div>
              <table class="table table-bordered text-center">
                <thead>
                  <tr>
                    <th>Periodo</th>
                    <th>Valor actual</th>
                    <th>Depreciación</th>
                    <th>Valor residual</th>
                  </tr>
                </thead>
                <tbody id="depreciationTableBody">

                </tbody>
              </table>
            </div>
