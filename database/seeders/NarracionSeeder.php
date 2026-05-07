<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Narracion;
use Illuminate\Support\Str;

class NarracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $narraciones = [
            [
                'titulo' => 'El café de la esquina',
                'contenido' => 'Cada mañana, el mismo ritual. La puerta de la cafetería cruje al abrirse, anunciando mi llegada. El aroma a granos recién tostados me envuelve como un abrazo familiar. En la mesa de siempre, junto a la ventana, espero verla entrar. Hoy como ayer, como hace tres años. La memoria juega trucos extraños, a veces creo que la veo sentada allí, sonriendo con esa complicidad que solo nosotros entendíamos. El camarero me trae mi café sin preguntar, doble de azúcar, como a ella le gustaba.',
                'slug' => 'el-cafe-de-la-esquina',
                'fecha_publicacion' => '2024-01-15',
                'estado' => 'publicado',
                'user_id' => 1,
                'orden' => 1000,
                'permiso_lectura' => 'publico',
                'count_feedback' => 0,
                'count_read' => 0,
            ],
            [
                'titulo' => 'Cartas que nunca llegaron',
                'contenido' => 'Bajo el colchón guardo las cartas. Cincuenta y tres exactamente, una por cada semana que pasó desde su partida. En cada una le cuento mi vida, mis miedos, mis pequeños triunfos. A veces me pregunto si alguna vez las leerá, si sabrá que cada palabra fue escrita con la tinta de mis lágrimas. La última carta la escribí ayer, pero no la guardé con las demás. La quemé en el patio, viendo cómo el fuego consumía las palabras que nunca tuvieron destinatario. A veces, dejar ir es la única forma de seguir adelante.',
                'slug' => 'cartas-que-nunca-llegaron',
                'fecha_publicacion' => '2024-02-20',
                'estado' => 'publicado',
                'user_id' => 1,
                'orden' => 2000,
                'permiso_lectura' => 'publico',
                'count_feedback' => 5,
                'count_read' => 23,
            ],
            [
                'titulo' => 'El último tren',
                'contenido' => 'La estación estaba vacía cuando llegué. El reloj marcaba las once de la noche, y el único sonido era el eco de mis pasos sobre el andén de madera. Sabía que el último tren partiría en cinco minutos, que esa era mi última oportunidad. Pero mis pies estaban clavados al suelo, atrapados entre el pasado y un futuro que no sabía cómo enfrentar. En el horizonte vi las luces del tren acercándose, y con ellas, la certeza de que algunas decisiones no se toman, simplemente suceden.',
                'slug' => 'el-ultimo-tren',
                'fecha_publicacion' => '2024-03-10',
                'estado' => 'publicado',
                'user_id' => 1,
                'orden' => 3000,
                'permiso_lectura' => 'publico',
                'count_feedback' => 2,
                'count_read' => 15,
            ],
            [
                'titulo' => 'Fragmentos de un sueño',
                'contenido' => 'No recuerdo cuándo comenzó, pero sé que no termina. En mis sueños, camino por calles que no conozco pero que siento como mi hogar. Hay una puerta azul siempre entreabierta, y detrás de ella, una voz que me llama por un nombre que no es el mío pero que reconozco como mío. Despierto con el sabor del mar en la boca y la sensación de haber dejado algo importante atrás. Quizás todos somos fragmentos de sueños ajenos, esperando que alguien nos complete.',
                'slug' => 'fragmentos-de-un-sueno',
                'fecha_publicacion' => '2024-04-05',
                'estado' => 'publicado',
                'user_id' => 1,
                'orden' => 4000,
                'permiso_lectura' => 'seguidores',
                'count_feedback' => 0,
                'count_read' => 8,
            ],
        ];

        foreach ($narraciones as $narracion) {
            Narracion::create($narracion);
        }

        // Actualizar slugs a cadenas simples
        $slugs = ['cafe-esquina', 'cartas-llegaron', 'ultimo-tren', 'suenos-fragmentos'];
        $ids = [1, 2, 3, 4];
        
        foreach ($ids as $index => $id) {
            Narracion::where('id', $id)->update(['slug' => $slugs[$index]]);
        }
    }
}
