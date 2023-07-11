@section('head')
    @vite(['resources/sass/app.scss'])
@endsection



<div class="container mx-auto">
    <h1>Usuarios</h1>
    <h2>Crear usuario</h2>
    <br>


    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

        <div class="mb-3 row d-flex align-items-center">
            <label for="name" class="col-sm-2 col-form-label">Nombre </label>
            <div class="col-sm-10">
                <input type="text" wire:model="name" class="form-control" name="name" id="name"
                    placeholder="José Carlos...">
                @error('nombre')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="surname" class="col-sm-2 col-form-label">Apellidos </label>
            <div class="col-sm-10">
                <input type="text" wire:model="surname" class="form-control" name="surname" id="surname"
                    placeholder="Pérez...">
                @error('surname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="role" class="col-sm-2 col-form-label">Rol </label>
            <div class="col-sm-10" wire:ignore.self>
                <select id="role" class="form-control js-example-responsive" wire:model="role">
                    <option value="admin">Admin</option>
                    <option value="alumno">Alumno</option>
                </select>
                @error('role')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="username" class="col-sm-2 col-form-label">Usuario </label>
            <div class="col-sm-10">
                <input type="text" wire:model="username" class="form-control" name="username" id="username"
                    placeholder="jose85">
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="password" class="col-sm-2 col-form-label">Contraseña </label>
            <div class="col-sm-10">
                <input type="password" wire:model="password" class="form-control" name="password" id="password"
                    placeholder="123456...">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-primary" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye" id="eye-icon"></i>
                </button>
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="email" class="col-sm-2 col-form-label">Email </label>
            <div class="col-sm-10">
                <input type="text" wire:model="email" class="form-control" name="email" id="email"
                    placeholder="jose85@hotmail.com ...">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>


    </form>
</div>





</div>

</tbody>
</table>
@section('scripts')
    <script>
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                'Octubre', 'Noviembre', 'Diciembre'
            ],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        document.addEventListener('livewire:load', function() {


        })
        $(document).ready(function() {
            console.log('select2')
            $("#datepicker").datepicker();

            $("#datepicker").on('change', function(e) {
                @this.set('fecha_nac', $('#datepicker').val());
            });

        });

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.className = "fas fa-eye-slash";
            } else {
                passwordInput.type = "password";
                eyeIcon.className = "fas fa-eye";
            }
        }
    </script>
@endsection
