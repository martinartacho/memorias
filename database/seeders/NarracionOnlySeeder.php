<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Narracion;
use Illuminate\Support\Str;

class NarracionOnlySeeder extends Seeder
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
                'slug' => 'cafe-esquina',
                'fecha_publicacion' => '2024-01-15',
                'estado' => 'publicado',
            ],
            [
                'titulo' => 'Cartas que nunca llegaron',
                'contenido' => 'Bajo el colchón guardo las cartas. Cincuenta y tres exactamente, una por cada semana que pasó desde su partida. En cada una le cuento mi vida, mis miedos, mis pequeños triunfos. A veces me pregunto si alguna vez las leerá, si sabrá que cada palabra fue escrita con la tinta de mis lágrimas. La última carta la escribí ayer, pero no la guardé con las demás. La quemé en el patio, viendo cómo el fuego consumía las palabras que nunca tuvieron destinatario. A veces, dejar ir es la única forma de seguir adelante.',
                'slug' => 'cartas-llegaron',
                'fecha_publicacion' => '2024-02-20',
                'estado' => 'publicado',
            ],
            [
                'titulo' => 'El último tren',
                'contenido' => 'La estación estaba vacía cuando llegué. El reloj marcaba las once de la noche, y el único sonido era el eco de mis pasos sobre el andén de madera. Sabía que el último tren partiría en cinco minutos, que esa era mi última oportunidad. Pero mis pies estaban clavados al suelo, atrapados entre el pasado y un futuro que no sabía cómo enfrentar. En el horizonte vi las luces del tren acercándose, y con ellas, la certeza de que algunas decisiones no se toman, simplemente suceden.',
                'slug' => 'ultimo-tren',
                'fecha_publicacion' => '2024-03-10',
                'estado' => 'publicado',
            ],
            [
                'titulo' => 'Fragmentos de un sueño',
                'contenido' => 'No recuerdo cuándo comenzó, pero sé que no termina. En mis sueños, camino por calles que no conozco pero que siento como mi hogar. Hay una puerta azul siempre entreabierta, y detrás de ella, una voz que me llama por un nombre que no es el mío pero que reconozco como mío. Despierto con el sabor del mar en la boca y la sensación de haber dejado algo importante atrás. Quizás todos somos fragmentos de sueños ajenos, esperando que alguien nos complete.',
                'slug' => 'suenos-fragmentos',
                'fecha_publicacion' => '2024-04-05',
                'estado' => 'publicado',
            ],
            [
                'titulo' => 'Memorias de otoño',
                'contenido' => 'Las hojas doradas caen sin prisa, como si supieran que su belleza efímera es su único propósito. Camino por el parque y cada hoja que se desprende me recuerda un momento, una palabra, una emoción guardada en el baúl de los recuerdos. El otoño siempre ha sido mi estación favorita, no solo por sus colores cálidos, pero porque me enseña que incluso la despedida puede ser hermosa. En cada hoja que baila al viento, veo la metáfora perfecta de la vida: breve, intensa y maravillosamente fugaz.',
                'slug' => 'memorias-otono',
                'fecha_publicacion' => '2024-05-20',
                'estado' => 'borrador',
            ],
        ];

        foreach ($narraciones as $narracion) {
            Narracion::create($narracion);
        }
    }
}
