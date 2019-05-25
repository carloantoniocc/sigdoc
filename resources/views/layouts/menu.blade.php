<div class="collapse navbar-collapse" id="app-navbar-collapse">		
	<!-- Left Side Of Navbar -->
	<ul class="nav navbar-nav">
		<!-- Authentication Links -->
		@if (!Auth::guest())
			
			@if( Auth::user()->isRole('Oficina de Partes') || Auth::user()->isRole('Convenios') || Auth::user()->isRole('Abastecimiento') || Auth::user()->isRole('Tesoreria') )
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
						Garantía <span class="caret"></span>
					</a>
					
					<ul class="dropdown-menu" role="menu">
						@if( Auth::user()->isRole('Oficina de Partes') )
							<li>
								<a href="{{ URL::to('garantias/oficinaPartes') }}"> Ingreso </a>
							</li>
						
							<li>
								<a href="{{ URL::to('garantias/oficinaPartes/nomina') }}"> Nóminas </a>
							</li>																		
							
							<li class="divider"></li>

							<li>
								<a href="{{ URL::to('garantias/oficinaPartes/listaNomina') }}"> Reporte Nóminas </a>
							</li>
						@endif
						
						@if( Auth::user()->isRole('Convenios') )	
							<li>
								<a href="{{ URL::to('garantias/convenios') }}"> Gestión de Garantía - Convenios </a>
							</li>
						@endif
						
						@if( Auth::user()->isRole('Abastecimiento') )		
							<li>
								<a href="{{ URL::to('garantias/abastecimiento') }}"> Gestión de Garantía - Abastecimiento </a>
							</li>
						@endif
						
						@if( Auth::user()->isRole('Tesoreria') )		
							<li>
								<a href="{{ URL::to('garantias/tesoreria') }}"> Cobro / Devolución </a>
							</li>
						@endif
					</ul>
				</li>
			@endif
		
			@if( Auth::user()->isRole('Oficina de Partes') )
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
						Oficina de Partes <span class="caret"></span>
					</a>
					
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="{{ URL::to('oficinaPartes') }}"> Ingreso </a>
						</li>

						<li>
							<a href="{{ URL::to('oficinaPartes/acepta') }}"> Ingreso Acepta </a>
						</li>

						<li>
							<a href="{{ URL::to('oficinaPartes/nomina') }}"> Nóminas </a>
						</li>
						
						<li class="divider"></li>

						<li>
							<a href="{{ URL::to('oficinaPartes/listaNomina') }}"> Reporte Nóminas </a>
						</li>	
					</ul>
				</li>
			@endif

			@if( Auth::user()->isRole('Secretaria de Convenios') || Auth::user()->isRole('Convenios') )
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
						Convenios <span class="caret"></span>
					</a>
					
					<ul class="dropdown-menu" role="menu">
						@if( Auth::user()->isRole('Secretaria de Convenios') )
							<li>
								<a href="{{ URL::to('secretariaConvenios') }}"> Secretaría de Convenios - Recepción </a>
							</li>
						@endif
						
						@if( Auth::user()->isRole('Convenios') )
							<li>
								<a href="{{ URL::to('oficinaConvenios/vistosBuenos') }}"> Envío a Visto Bueno </a>
							</li>
							
							<li>
								<a href="{{ URL::to('oficinaConvenios/validados') }}"> Documentos con V°B° Validados </a>
							</li>
						@endif	
						
						
						@if( Auth::user()->isRole('Convenios') )
							<li class="divider"></li>
							
							<li>
								<a href="{{ URL::to('convenios') }}"> Gestión de Convenios </a>
							</li>
							
							<li>
								<a href="{{ URL::to('oficinaConvenios/documentosAdjuntar') }}"> Adjuntar Validadores </a>
							</li>
						@endif
						
					</ul>
				</li>	
			@endif	
			
			@if( Auth::user()->isRole('Referente Tecnico') || Auth::user()->isRole('Jefe Referente Tecnico') )	
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
						Referente Técnico <span class="caret"></span>
					</a>
					
					<ul class="dropdown-menu" role="menu">
						@if( Auth::user()->isRole('Referente Tecnico') )	
							<li>
								<a href="{{ URL::to('referenteTecnico') }}"> Por Validar </a>
							</li>
						@endif

						@if( Auth::user()->isRole('Jefe Referente Tecnico') )	
							<li>
								<a href="{{ URL::to('jefeReferenteTecnico') }}"> Validar Documentos </a>
							</li>
						@endif
						
						<li class="divider"></li>
						
						@if( Auth::user()->isRole('Referente Tecnico') )	
							<li>
								<a href="{{ URL::to('cargaValidadores/index') }}"> Validador - Carga Masiva </a>
							</li>
						@endif						
						
						@if( Auth::user()->isRole('Referente Tecnico') || Auth::user()->isRole('Jefe Referente Tecnico') )
							<li>
								<a href="{{ URL::to('documentosValidados') }}"> Documentos Validados </a>
							</li>
						@endif	
					</ul>
				</li>	
			@endif		
			
			@if( Auth::user()->isRole('Contabilidad') || Auth::user()->isRole('Tesoreria') )	
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
						Finanzas <span class="caret"></span>
					</a>
					
					<ul class="dropdown-menu" role="menu">
						@if( Auth::user()->isRole('Contabilidad') )	
							<li>
								<a href="{{ URL::to('contabilidad') }}"> Contabilidad </a>
							</li>
							<li>
								<a href="{{ URL::to('contabilidad/reporteContabilidad') }}">Reporte Contabilidad </a>
							</li>
							<li class="divider"></li>
						@endif
						
						@if( Auth::user()->isRole('Tesoreria') )	
							<li>
								<a href="{{ URL::to('tesoreria') }}"> Tesorería - Pago </a>
							</li>
							
							<li>
								<a href="{{ URL::to('tesoreria/entrega') }}"> Tesorería - Entrega </a>
							</li>							
							
							<li>
								<a href="{{ URL::to('tesoreria/sigfe') }}"> Tesorería - Carga Masiva </a>
							</li>
							
						@endif
					</ul>	
					
				</li>	
			@endif		
			
			<!-- REPORTES -->
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
					Reportes <span class="caret"></span>
				</a>
				
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="{{ URL::to('documentos/reporte') }}"> Consulta Documentos </a>
					</li>
					
					@if( Auth::user()->isRole('Oficina de Partes') || Auth::user()->isRole('Convenios') || Auth::user()->isRole('Abastecimiento') || Auth::user()->isRole('Tesoreria') )
						<li class="divider"></li>
						
						<li>
							<a href="{{ URL::to('garantias/reporte') }}"> Consulta Documentos de Garantía </a>
						</li>
					@endif		
				</ul>
			</li>
			
			<!-- MANUALES -->
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
					Manuales de Usuario <span class="caret"></span>
				</a>
				
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="{{ asset('/manuales/OficinaPartes.pdf') }}" aria-expanded="false" target="_blank"> Oficina de Partes </a>
					</li>
					<li>
						<a href="{{ asset('/manuales/Manual_Acepta.pdf') }}" aria-expanded="false" target="_blank"> Oficina de Partes - Acepta </a>
					</li>					
					<li>
						<a href="{{ asset('/manuales/SecretariaConvenios.pdf') }}" aria-expanded="false" target="_blank"> Secretaría de Convenios </a>
					</li>
					<li>
						<a href="{{ asset('/manuales/Convenios.pdf') }}" aria-expanded="false" target="_blank"> Convenios </a>
					</li>
					<li>
						<a href="{{ asset('/manuales/ReferenteTecnico.pdf') }}" aria-expanded="false" target="_blank"> Referente Técnico </a>
					</li>
					<li>
						<a href="{{ asset('/manuales/JefeReferente.pdf') }}" aria-expanded="false" target="_blank"> Jefe Referente Técnico </a>
					</li>
					<li>
						<a href="{{ asset('/manuales/Contabilidad.pdf') }}" aria-expanded="false" target="_blank"> Contabilidad </a>
					</li>
					<li>
						<a href="{{ asset('/manuales/Tesoreria.pdf') }}" aria-expanded="false" target="_blank"> Tesorería </a>
					</li>
					<li class="divider"></li>
					<li>
						<a href="{{ asset('/manuales/OficinaPartes-Garantias.pdf') }}" aria-expanded="false" target="_blank"> Oficina de Partes - Garantía</a>
					</li>
					<li>
						<a href="{{ asset('/manuales/Convenios-Garantias.pdf') }}" aria-expanded="false" target="_blank"> Convenios / Abastecimiento - Garantía </a>
					</li>
					<li>
						<a href="{{ asset('/manuales/Tesoreria-Garantias.pdf') }}" aria-expanded="false" target="_blank"> Tesorería - Garantía </a>
					</li>
				</ul>
			</li>

			<!-- MANUALES -->
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
					Informes <span class="caret"></span>
				</a>
				
				<ul class="dropdown-menu" role="menu">
				
						<li>
							<a href="http://10.8.64.41/repo/"> Reportes </a>
						</li>
				</ul>
			</li>









			@if( Auth::user()->isRole('Administrador') )
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
						Administración <span class="caret"></span>
					</a>
					
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="{{ URL::to('users') }}"> Usuarios </a>
						</li>
						
						<li>
							<a href="{{ URL::to('referentes') }}"> Referentes </a>
						</li>
						
						<li>
							<a href="{{ URL::to('firmantes') }}"> Firmantes </a>
						</li>
						
						<li class="divider"></li>
						
						<li>
							<a href="{{ URL::to('comunas') }}"> Comunas </a>
						</li>
						
						<li>
							<a href="{{ URL::to('tipoEstabs') }}"> Tipo de Establecimiento </a>
						</li>
						
						<li>
							<a href="{{ URL::to('establecimientos') }}"> Establecimientos </a>
						</li>
						
						<li class="divider"></li>
						
						<li>
							<a href="{{ URL::to('tipoDocs') }}"> Tipo de Documentos </a>
						</li>
						
						<li>
							<a href="{{ URL::to('tipoCompras') }}"> Tipo de Compras </a>
						</li>
						
						<li>
							<a href="{{ URL::to('validadors') }}"> Validadores </a>
						</li>

						@if( Auth::user()->isRole('Administrar Proveedores') )
						<li>
							<a href="{{ URL::to('proveedors') }}"> Proveedores </a>
						</li>
						@endif

						<li class="divider"></li>

						<li>
							<a href="{{ URL::to('bancos') }}"> Bancos </a>
						</li>
						
						<li>
							<a href="{{ URL::to('clasificadors') }}"> Clasificadores </a>
						</li>
						
						<li>
							<a href="{{ URL::to('cuentas') }}"> Cuentas </a>
						</li>
						
						<li>
							<a href="{{ URL::to('tipoPagos') }}"> Tipo de Pagos </a>
						</li>

						<li>
							<a href="{{ URL::to('objetoGarantias') }}"> Objetos de Garantía </a>
						</li>

						<li>
							<a href="{{ URL::to('monedas') }}"> Monedas </a>
						</li>

						<li class="divider"></li>
						
						<li>
							<a href="{{ URL::to('administrador/documentos') }}"> Editar Documentos </a>
						</li>	
						


					</ul>
				</li>		
			@endif
			
		@endif
	</ul>

	<!-- Right Side Of Navbar -->
	<ul class="nav navbar-nav navbar-right">
		<!-- Authentication Links -->
		@if (!Auth::guest())
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#ffffff">
					{{ Auth::user()->name }} <span class="caret"></span>
				</a>

				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="{{ URL::to('users/password/cambiar') }}"> Cambiar Contraseña </a>
					</li>
					
					<li>
						<a href="{{ route('logout') }}"
							onclick="event.preventDefault();
									 document.getElementById('logout-form').submit();">
							Salir
						</a>

						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
					</li>
				</ul>
			</li>
		@endif
	</ul>
</div>