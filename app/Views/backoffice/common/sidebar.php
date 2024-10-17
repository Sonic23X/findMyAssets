

      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <div class="sidebar">
          <div class="user-panel mt-3 pb-3 mb-3">
            <div class="sidebar-profile">
              <div class="image">
                <img src="./images/backoffice/usuario.png" class="img-circle" alt="User Image">
              </div>
              <br>
              <div class="mt-2 mr-5">
                <a href="<?= base_url( '/perfil' ) ?>" class="d-block">Hola, <?= $name ?></a>
                <br>
                <span style="color: #C2C7D0;">Empresa:</span>
                <br>
                <select id="combo-empresas" class="custom-select">
                <?php
                  foreach($empresas as $empresa)
                  {
                    if($empresa->id_empresa == $actual)
                      echo "<option value='".$empresa->id_empresa."' selected>".$empresa->nombre."</option>";
                    else
                      echo "<option value='".$empresa->id_empresa."'>".$empresa->nombre."</option>";
                  }
                ?>
                </select>
              </div>
            </div>
          </div>
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                <a href="<?= base_url( '/dashboard' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-home"></i>
                  Inicio
                </a>
              </li>
              <li class="nav-header">Activos</li>
              <li class="nav-item">
                <a href="<?= base_url( '/alta' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-qrcode"></i>
                  Escaner
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url( '/carga' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-cloud-upload-alt"></i>
                  Carga de activos
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url( '/bajas' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-cloud-download-alt"></i>
                  Baja
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url( '/depreciacion' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-money-bill"></i>
                  Depreciación
                </a>
              </li>
              <li class="nav-header">Mantenedores</li>
              <li class="nav-item">
                <a href="<?= base_url( '/inventario' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-warehouse"></i>
                  Inventario
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url( '/usuarios' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-user-plus"></i>
                  Usuarios
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url( '/empresas' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-hotel"></i>
                  Empresas y locaciones
                </a>
              </li>
              <li class="nav-item d-none">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-laptop"></i>
                  Tipos de activos
                </a>
              </li>
              <li class="nav-header"></li>
              <li class="nav-header"></li>
              <li class="nav-item d-none">
                <a href="<?= base_url( '/pagos' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-id-card"></i>
                  Medios de pago y facturación
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url( '/salir' ) ?>" class="nav-link">
                  <i class="nav-icon fas fa-sign-out-alt"></i>
                  Cerrar sesión
                </a>
              </li>
            </ul>
          </nav>

        </div>

      </aside>
