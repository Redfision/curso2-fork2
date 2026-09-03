<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Widgets\AvisosStats;
use App\Livewire\BuscadorAvisos;
use App\Models\Categoria;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Recorre la sesion 4 completa (guias 01 y 02 + tarea) contra una base
 * sqlite en memoria. Corre con: php artisan test --filter Sesion4Test
 */
class Sesion4Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $editor;
    private Post $ajeno;
    private Post $propio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@blog.test')->firstOrFail();
        $this->editor = User::where('email', 'editor@blog.test')->firstOrFail();
        $this->ajeno = Post::where('user_id', $this->admin->id)->firstOrFail();
        $this->propio = Post::create([
            'titulo' => 'Aviso del editor',
            'contenido' => 'Contenido del editor',
            'categoria_id' => Categoria::first()->id,
            'publicado' => true,
            'user_id' => $this->editor->id,
        ]);
    }

    public function test_parte_0_el_panel_existe_y_pide_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_parte_1_y_1_5_la_lista_de_avisos_abre_en_espanol(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Avisos')
            ->assertSee($this->ajeno->titulo);

        Livewire::actingAs($this->admin)
            ->test(ListPosts::class)
            ->assertCanSeeTableRecords(Post::all());
    }

    public function test_parte_2_crear_desde_el_panel_asigna_el_dueno_y_usa_la_relacion(): void
    {
        Livewire::actingAs($this->editor)
            ->test(CreatePost::class)
            ->fillForm([
                'titulo' => 'Creado desde el panel',
                'categoria_id' => Categoria::first()->id,
                'contenido' => 'Texto del aviso',
                'publicado' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::where('titulo', 'Creado desde el panel')->firstOrFail();
        $this->assertSame($this->editor->id, $post->user_id);
        $this->assertSame(Categoria::first()->nombre, $post->categoria->nombre);
    }

    public function test_parte_2_la_validacion_vive_en_el_campo(): void
    {
        Livewire::actingAs($this->editor)
            ->test(CreatePost::class)
            ->fillForm(['titulo' => '', 'contenido' => ''])
            ->call('create')
            ->assertHasFormErrors(['titulo' => 'required', 'contenido' => 'required', 'categoria_id' => 'required']);
    }

    public function test_policies_paso_1_el_editor_solo_ve_editar_y_borrar_en_lo_suyo(): void
    {
        Livewire::actingAs($this->editor)
            ->test(ListPosts::class)
            ->assertActionVisible(TestAction::make('edit')->table($this->propio))
            ->assertActionVisible(TestAction::make('delete')->table($this->propio))
            ->assertActionHidden(TestAction::make('edit')->table($this->ajeno))
            ->assertActionHidden(TestAction::make('delete')->table($this->ajeno));

        $this->actingAs($this->editor)
            ->get(PostResource::getUrl('edit', ['record' => $this->ajeno]))
            ->assertForbidden();

        $this->actingAs($this->editor)
            ->get(PostResource::getUrl('edit', ['record' => $this->propio]))
            ->assertOk();
    }

    public function test_policies_paso_1_el_admin_puede_con_todo(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ListPosts::class)
            ->assertActionVisible(TestAction::make('edit')->table($this->propio))
            ->assertActionVisible(TestAction::make('delete')->table($this->propio));

        $this->actingAs($this->admin)
            ->get(PostResource::getUrl('edit', ['record' => $this->propio]))
            ->assertOk();
    }

    public function test_policies_paso_3_el_borrado_masivo_queda_cerrado_para_el_editor(): void
    {
        $this->actingAs($this->editor);
        $this->assertFalse(PostResource::canDeleteAny());

        $this->actingAs($this->admin);
        $this->assertTrue(PostResource::canDeleteAny());
    }

    public function test_nivel_1_el_campo_resumen_se_guarda_y_se_ve_en_la_tabla(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreatePost::class)
            ->fillForm([
                'titulo' => 'Con resumen',
                'categoria_id' => Categoria::first()->id,
                'contenido' => 'Texto',
                'resumen' => 'Resumen corto del aviso',
                'publicado' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('Resumen corto del aviso', Post::where('titulo', 'Con resumen')->value('resumen'));

        Livewire::actingAs($this->admin)
            ->test(ListPosts::class)
            ->assertCanRenderTableColumn('resumen');
    }

    public function test_nivel_2_el_filtro_por_categoria_y_el_ternario_funcionan(): void
    {
        $categoria = Categoria::first();
        $otra = Categoria::where('id', '!=', $categoria->id)->first();
        $borrador = Post::create([
            'titulo' => 'Borrador pendiente', 'contenido' => 'x', 'publicado' => false,
            'categoria_id' => $otra->id, 'user_id' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListPosts::class)
            ->filterTable('categoria_id', $categoria->id)
            ->assertCanSeeTableRecords(Post::where('categoria_id', $categoria->id)->get())
            ->assertCanNotSeeTableRecords(Post::where('categoria_id', '!=', $categoria->id)->get())
            ->resetTableFilters()
            ->filterTable('publicado', false)
            ->assertCanSeeTableRecords([$borrador])
            ->assertCanNotSeeTableRecords([$this->propio]);
    }

    public function test_nivel_2_la_accion_publicar_aparece_solo_en_borradores_y_publica(): void
    {
        $borrador = Post::create([
            'titulo' => 'Borrador pendiente', 'contenido' => 'x', 'publicado' => false,
            'categoria_id' => Categoria::first()->id, 'user_id' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListPosts::class)
            ->assertActionVisible(TestAction::make('publicar')->table($borrador))
            ->assertActionHidden(TestAction::make('publicar')->table($this->propio))
            ->callAction(TestAction::make('publicar')->table($borrador))
            ->assertHasNoActionErrors();

        $this->assertTrue($borrador->fresh()->publicado);
    }

    public function test_nivel_3_el_widget_del_dashboard_cuenta_avisos(): void
    {
        Post::create([
            'titulo' => 'Borrador', 'contenido' => 'x', 'publicado' => false,
            'categoria_id' => Categoria::first()->id, 'user_id' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AvisosStats::class)
            ->assertSee('Avisos')
            ->assertSee(Post::publicados()->count() . ' publicados')
            ->assertSee('Borradores');
    }

    public function test_tarea_nivel_2_can_access_panel_deja_fuera_a_quien_no_es_admin_ni_editor(): void
    {
        $visitante = User::factory()->create();
        $visitante->rol = 'visitante';
        $visitante->save();

        $this->actingAs($visitante)->get('/admin/posts')->assertForbidden();
        $this->actingAs($this->editor)->get('/admin/posts')->assertOk();
    }

    public function test_nivel_extra_el_buscador_livewire_filtra_la_portada(): void
    {
        Livewire::test(BuscadorAvisos::class)
            ->assertSee('Cambio de horario')
            ->set('busqueda', 'horario')
            ->assertSee('Cambio de horario')
            ->assertDontSee('Curso de primeros auxilios')
            ->call('limpiar')
            ->assertSet('busqueda', '')
            ->assertSee('Curso de primeros auxilios');
    }
}
