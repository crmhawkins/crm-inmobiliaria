<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Caracteristicas;
use App\Models\TipoVivienda;
use App\Models\User;
use Livewire\Component;
use App\Models\Inmuebles;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Clientes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Empty_;
use App\Http\Controllers\InmueblesController;
use Illuminate\Support\Facades\Log;

class Show extends Component
{
    use LivewireAlert;

    public $identificador;

    public $inmuebles;
    public $caracteristicas;

    public $titulo;
    public $descripcion;
    public $m2;
    public $m2_construidos;
    public $valor_referencia;
    public $habitaciones;
    public $banos;
    public $cod_postal;
    public $tipo_vivienda_id;
    public $ubicacion;
    public $cert_energetico;
    public $cert_energetico_elegido;
    public $inmobiliaria = null;
    public $estado;
    public $disponibilidad;
    public $otras_caracteristicasArray = [];

    public $otras_caracteristicas;
    public $referencia_catastral;

    public $vendedores;
    public $vendedor_id;
    public $vendedor_nombre;
    public $vendedor_dni;
    public $vendedor_ubicacion;
    public $vendedor_telefono;
    public $vendedor_correo;
    public $tipos_vivienda;
    public $ruta_imagenes;
    public $imagenes_correo = [];
    public $galeriaArray = [];
    public $galeria;
    public $clientes;
    protected $listeners = ['fileSelected'];
    public $key;
    public $data;
    public $publicado;

    public function mount()
    {   $this->key = env('Api_key');
        $this->inmuebles = Inmuebles::find($this->identificador);
        $this->tipos_vivienda = TipoVivienda::all();
        $this->vendedores = User::all();
        $this->caracteristicas = Caracteristicas::all();
        $this->clientes = Clientes::all();

        $this->titulo = $this->inmuebles->titulo;
        $this->descripcion = $this->inmuebles->descripcion;
        $this->m2 = $this->inmuebles->m2;
        $this->m2_construidos = $this->inmuebles->m2_construidos;
        $this->valor_referencia = $this->inmuebles->valor_referencia;
        $this->habitaciones = $this->inmuebles->habitaciones;
        $this->banos = $this->inmuebles->banos;
        $this->cod_postal = $this->inmuebles->cod_postal;
        $this->tipo_vivienda_id = $this->inmuebles->tipo_vivienda_id;
        $this->ubicacion = $this->inmuebles->ubicacion;
        $this->cert_energetico = $this->inmuebles->cert_energetico;
        $this->cert_energetico_elegido = $this->inmuebles->cert_energetico_elegido;
        if ($this->inmuebles->inmobiliaria != null) {
            $this->inmobiliaria = null;
        } else {
            $this->inmobiliaria = true;
        };
        $this->estado = $this->inmuebles->estado;
        $this->disponibilidad = $this->inmuebles->disponibilidad;
        $this->otras_caracteristicasArray = json_decode($this->inmuebles->otras_caracteristicas, true);
        $this->referencia_catastral = $this->inmuebles->referencia_catastral;
        if ($this->inmuebles->galeria != null) {
            $this->galeriaArray = json_decode($this->inmuebles->galeria, true);
        } else {
            $this->galeriaArray = [];
        }
        $this->vendedor_id = $this->inmuebles->vendedor_id;

        if ($this->vendedor_id == "") {
            $this->vendedor_nombre = "";
            $this->vendedor_dni = "";
            $this->vendedor_ubicacion = "";
            $this->vendedor_telefono = "";
            $this->vendedor_correo = "";
        } else {
            $vendedor = User::where('id', $this->vendedor_id)->first();
            $this->vendedor_nombre = $vendedor->nombre_completo;
            $this->vendedor_dni = $vendedor->dni;
            $this->vendedor_ubicacion  = $vendedor->ubicacion;
            $this->vendedor_telefono = $vendedor->telefono;
            $this->vendedor_correo = $vendedor->email;
        }

        $this->data = [
            "ExternalId" => "1519",        //The unique identifier for a property                                                          string
            "AgencyReference" => "1634",         //Reference given by the agency to the property                                                 string
            "TypeId" => 1,                      //Identifies the type of the property within the following enumeration                          id de DIC_Building_Type
            "SubTypeId" => 9,                   //Optional, Indicates the subtype of the property from the value selected in 'TypeId' field     id de DIC_Building_Subtype
            "ContactTypeId" => 3,               // Defines the type of contact provided                                                         id de DIC_ContactType
            "PropertyAddress" => [              //Defines where is exactly located the property                                                 array
                [
                    "ZipCode" => "39700",       //ZIP Code (not required if x and y are specified)                                              string
                    "FloorId" => 6,
                    "x" => -3.21288689804,
                    "y" => 43.3397409074,
                    "VisibilityModeId" => 1,
                    "Street" => "Siempre Viva", //Free text. The name of the street                                                             string
                    "Number" => "27",            //Number of the street                                                                         string
                ]
            ],
            "PropertyDocument" =>[
                [
                    "TypeId" => 1,
                    "Url" => "https://grupocerban.com/storage/photos/1/imagentest1.jpg",
                    "SortingId" => 1,
                ],
                [
                    "TypeId" => 1,
                    "Url" => "https://grupocerban.com/storage/photos/1/imagentest2.jpg",
                    "SortingId" => 2,
                ],
                [
                    "TypeId" => 1,
                    "Url" => "https://grupocerban.com/storage/photos/1/imagentest3.jpg",
                    "SortingId" => 3,
                ]
                ],
            "PropertyFeature" => [
                [
                    "FeatureId" => 1,
                    "DecimalValue" => 58,
                ],
                [
                    "FeatureId" => 2,
                    "TextValue" => "Inmueble 1."
                ],
                [
                    "FeatureId" => 3,
                    "TextValue" => "Inmobiliaria vende piso reformado en la zona de Brazomar. La vivienda se distribuye en 2 habitaciones, 1 baño, cocina equipada y un amplio salón comedor con salida a una terraza."
                ],
                [
                    "FeatureId" => 323,
                    "DecimalValue" => 1,
                ],
                [
                    "FeatureId" => 324,
                    "DecimalValue" => 1,
                ],
                [
                    "FeatureId" => 325,
                    "DecimalValue" => 1,
                ],
                [
                    "FeatureId" => 326,
                    "DecimalValue" => 1,
                ],
                [
                    "FeatureId" => 327,
                    "DecimalValue" => 1,
                ],
                [
                    "FeatureId" => 249,
                    "DecimalValue" => 1,
                ],
                [
                    "FeatureId" => 11,
                    "DecimalValue" => 5,
                ],
                [
                    "FeatureId" => 12,
                    "DecimalValue" => 2,
                ],
                [
                    "FeatureId" => 30,
                    "BoolValue" => true,
                ],
            ],
            "PropertyContactInfo" => [
                [
                    "TypeId" => 1,
                    "Value" => "demo@adevinta.com",
                ],
                [
                    "TypeId" => 2,
                    "Value" => "942862711",
                ]
            ],
            "PropertyTransaction" => [
                [
                    "TransactionTypeId" => 1,
                    "Price" => 160000,
                    "ShowPrice" => true
                ]
            ]

        ];

        $this->publicado = false;
        $publicaciones = json_decode($this->apiGet());
        if (!empty($publicaciones)) {
            foreach ($publicaciones as $publicacion) {
               // dd($publicacion);
                if ($publicacion->ExternalId ==  $this->data['ExternalId']) {
                    $this->publicado = true;
                    break;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.inmuebles.show');
    }

    public function update()
    {
        if ($this->inmobiliaria == null) {
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $this->inmobiliaria = true;
            } else {
                $this->inmobiliaria = false;
            }
        } else {
            $this->inmobiliaria = null;
        }

        $this->otras_caracteristicas = json_encode($this->otras_caracteristicasArray);
        $this->galeria = json_encode($this->galeriaArray);

        $validatedData = $this->validate(
            [
                'titulo' => 'required',
                'descripcion' => 'required',
                'm2' => 'required',
                'm2_construidos' => 'required',
                'valor_referencia' => 'required',
                'habitaciones' => 'required',
                'banos' => 'required',
                'tipo_vivienda_id' => 'required',
                'vendedor_id' => 'required',
                'ubicacion' => 'required',
                'cod_postal' => 'required',
                'cert_energetico' => 'required',
                'cert_energetico_elegido' => 'nullable',
                'estado' => 'required',
                'galeria' => 'nullable',
                'disponibilidad' => 'required',
                'otras_caracteristicas' => 'nullable',
                'referencia_catastral' => 'required',
                'inmobiliaria' => 'nullable',
            ],
            // Mensajes de error
            [
                'titulo.required' => 'El título es obligatorio.',
                'descripcion.required' => 'Se requiere añadir descripción.',
                'm2.required' => 'Indica los m2 del inmueble.',
                'm2_construidos.required' => 'Indica los m2 construidos del inmueble.',
                'valor_referencia.required' => 'Indica el valor de referencia del inmueble.',
                'habitaciones.required' => 'Indica las habitaciones del inmueble.',
                'banos.required' => 'Indica los baños del inmueble.',
                'tipo_vivienda_id.required' => 'Indica el tipo de vivienda del inmueble.',
                'vendedor_id.required' => 'Indica al vendedor del inmueble.',
                'ubicacion.required' => 'Indica la ubicación del inmueble.',
                'cod_postal.required' => 'El código postal es obligatorio.',
                'cert_energetico.required' => 'Indica si existe un certificado energético o no.',
            ]
        );

        // Guardar datos validados
        // Encuentra el alumno identificado
        $inmuebles = Inmuebles::find($this->identificador);

        // Guardar datos validados
        $inmueblesSave = $inmuebles->update([
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'm2' => $this->m2,
            'm2_construidos' => $this->m2_construidos,
            'valor_referencia' => $this->valor_referencia,
            'habitaciones' => $this->habitaciones,
            'banos' => $this->banos,
            'tipo_vivienda_id' => $this->tipo_vivienda_id,
            'vendedor_id' => $this->vendedor_id,
            'ubicacion' => $this->ubicacion,
            'cod_postal' => $this->cod_postal,
            'cert_energetico' => $this->cert_energetico,
            'cert_energetico_elegido' => $this->cert_energetico_elegido,
            'estado' => $this->estado,
            'galeria' => $this->galeria,
            'disponibilidad' => $this->disponibilidad,
            'otras_caracteristicas' => $this->otras_caracteristicas,
            'referencia_catastral' => $this->referencia_catastral,
            'inmobiliaria' => $this->inmobiliaria

        ]);

        // Alertas de guardado exitoso
        if ($inmueblesSave) {
            // Recargar el modelo para tener los datos actualizados
            $inmuebles->refresh();

            // Actualizar en Idealista si está sincronizado
            if ($inmuebles->idealista_property_id) {
                try {
                    $controller = new InmueblesController();
                    $controller->updateIdealistaProperty(new \Illuminate\Http\Request(), $inmuebles->id);
                    Log::info('Inmueble actualizado en Idealista desde Show', [
                        'inmueble_id' => $inmuebles->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error actualizando en Idealista desde Show', [
                        'inmueble_id' => $inmuebles->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Actualizar en Fotocasa si está publicado
            try {
                $controller = new InmueblesController();
                $controller->sendToFotocasa($inmuebles);
                Log::info('Inmueble actualizado en Fotocasa desde Show', [
                    'inmueble_id' => $inmuebles->id
                ]);
            } catch (\Exception $e) {
                Log::error('Error actualizando en Fotocasa desde Show', [
                    'inmueble_id' => $inmuebles->id,
                    'error' => $e->getMessage()
                ]);
            }

            $this->alert('success', '¡Inmuebles actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del inmuebles!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function destroy()
    {
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el inmuebles? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('inmuebles.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $inmuebles = Inmuebles::find($this->identificador);
        $inmuebles->delete();
        return redirect()->route('inmuebles.index');
    }
    public function fileSelected($url)
    {

        $this->ruta_imagenes = $url;

        // puedes realizar acciones aquí, como almacenar la URL en la base de datos
    }

    public function addGaleria()
    {
        if (!in_array($this->ruta_imagenes, $this->galeriaArray)) {
            $this->galeriaArray[count($this->galeriaArray) + 1] = $this->ruta_imagenes;
            $this->emit('refreshGalleryComponent', $this->galeriaArray);
        }
    }

    public function eliminarImagen($id)
    {
        unset($this->galeriaArray[$id]);
    }

    public function updated()
    {
        if ($this->vendedor_id == "") {
            $this->vendedor_nombre = "";
            $this->vendedor_dni = "";
            $this->vendedor_ubicacion = "";
            $this->vendedor_telefono = "";
            $this->vendedor_correo = "";
        } else {
            $vendedor = User::where('id', $this->vendedor_id)->first();
            $this->vendedor_nombre = $vendedor->nombre_completo;
            $this->vendedor_dni = $vendedor->dni;
            $this->vendedor_ubicacion  = $vendedor->ubicacion;
            $this->vendedor_telefono = $vendedor->telefono;
            $this->vendedor_correo = $vendedor->email;
        }
    }
    public function deleteImagen($key)
    {
        if (count($this->imagenes_correo) == 1) {
            $this->imagenes_correo = [];
        } else {
            unset($this->imagenes_correo[$key]);
            $this->imagenes_correo = array_values($this->imagenes_correo);
        }
    }

    public function enviarCorreoImagenes($inmueble_id)
    {

        $inmueble = Inmuebles::where('id', $inmueble_id)->first();
        $cliente = Clientes::where('id', $this->cliente_id)->first();

        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $nombre_inmobiliaria = "INMOBILIARIA SAYCO";
        } else {
            $nombre_inmobiliaria = "INMOBILIARIA SANCER";
        }

        $imagenes_adjuntadas = [];

        foreach (json_decode($inmueble->galeria, true) as $key => $imagen) {
            if (in_array($key, $this->imagenes_correo)) {
                $imagenes_adjuntadas[] = $imagen;
            }
        }

        $texto = 'Buenas, ' . $cliente->nombre . '. Te enviamos una selección de imágenes del inmueble ' . $inmueble->titulo;

        Mail::raw($texto, function ($message) use ($cliente, $nombre_inmobiliaria, $inmueble, $imagenes_adjuntadas) {
            $message->from('admin@grupocerban.com', $nombre_inmobiliaria);
            $message->to($cliente->email, $cliente->nombre_completo);
            $message->to(env('MAIL_USERNAME'));
            $message->subject($nombre_inmobiliaria . " - Imágenes del inmueble" . $inmueble->titulo);

            foreach ($imagenes_adjuntadas as $ruta_imagen) {
                $message->attach($ruta_imagen);
            }
        });
    }

    public function addImagen($key)
    {
        $this->imagenes_correo[] = $key;
    }


    public function apiGet()
    {
        $url = "https://api.inmofactory.com/api/property";
        $response = Http::withHeaders([
            "X-Source" =>"12ad21a2-b568-4751-a34e-f5533db78c4c",
            'Inmofactory-Api-Key' =>  $this->key,
        ])->withoutVerifying()->get($url);

        $this->saveResponseToFile('get_response.txt', $response->body());

        return $response->json();
    }

    /**
     * Ejecuta una solicitud POST a la API de Inmofactory.
     */
    public function apiPost()
    {
        $url = "https://api.inmofactory.com/api/property";
        $response = Http::withHeaders([
            "X-Source" =>"12ad21a2-b568-4751-a34e-f5533db78c4c",
            'Inmofactory-Api-Key' =>  $this->key,
        ])->withoutVerifying()->post($url, $this->data);
        $responseBody = $response->json(); // Esto te dará el array de la respuesta
        $jsonResponse = json_encode($responseBody, JSON_PRETTY_PRINT); // Convierte a cadena JSON

        // Guarda los datos de la solicitud y la respuesta en archivos
        $this->saveResponseToFile('post_json.txt', json_encode($this->data, JSON_PRETTY_PRINT)); // También conviertes a string
        $this->saveResponseToFile('post_response.txt', $jsonResponse); // Aquí ya guardas el JSON como string

        return $response->json();
    }

    /**
     * Ejecuta una solicitud PUT a la API de Inmofactory.
     */
    public function apiPut()
    {
        $url = "https://api.inmofactory.com/api/property";
        $response = Http::withHeaders([
            "X-Source" =>"12ad21a2-b568-4751-a34e-f5533db78c4c",
            'Inmofactory-Api-Key' =>  $this->key,
        ])->withoutVerifying()->put($url, $this->data);
        $responseBody = $response->json(); // Esto te dará el array de la respuesta
        $jsonResponse = json_encode($responseBody, JSON_PRETTY_PRINT); // Convierte a cadena JSON

        // Guarda los datos de la solicitud y la respuesta en archivos
        $this->saveResponseToFile('put_json.txt', json_encode($this->data, JSON_PRETTY_PRINT)); // También conviertes a string
        $this->saveResponseToFile('put_response.txt', $jsonResponse); // Aquí ya guardas el JSON como string

        return $response->json();
    }

    /**
     * Ejecuta una solicitud DELETE a la API de Inmofactory.
     */
    public function apiDelete()
    {
        $url = "https://api.inmofactory.com/api/v2/property/" . base64_encode($this->data['ExternalId']);
        $response = Http::withHeaders([
            "X-Source" =>"12ad21a2-b568-4751-a34e-f5533db78c4c",
            'Inmofactory-Api-Key' =>  $this->key,
        ])->withoutVerifying()->delete($url);

        $this->saveResponseToFile('delete_response.txt', $response->body());

        return $response->json();
    }

    public function saveResponseToFile($filename, $content)
    {
        Storage::disk('local')->put($filename, $content);
    }

}
