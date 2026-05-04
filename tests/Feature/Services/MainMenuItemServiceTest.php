<?php

namespace Tests\Feature\Services;

use App\Models\MainMenuItem;
use App\Services\Admin\MainMenuItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainMenuItemServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): MainMenuItemService
    {
        return new MainMenuItemService;
    }

    // -------------------------------------------------------------------------
    // createItem
    // -------------------------------------------------------------------------

    public function test_create_item_assigns_sort_no_one_to_first_child_of_root(): void
    {
        $item = $this->service()->createItem([
            'parent_id' => 0,
            'title' => 'First',
            'slug' => 'first',
            'is_active' => true,
        ]);

        $this->assertSame(1, $item->sort_no);
    }

    public function test_create_item_appends_after_existing_siblings(): void
    {
        MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 1, 'title' => 'A']);
        MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 2, 'title' => 'B']);

        $item = $this->service()->createItem([
            'parent_id' => 0,
            'title' => 'C',
            'slug' => 'c',
            'is_active' => true,
        ]);

        $this->assertSame(3, $item->sort_no);
    }

    public function test_create_item_sort_no_is_scoped_to_parent(): void
    {
        $parentA = MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 1, 'title' => 'Parent A']);
        MainMenuItem::factory()->childOf($parentA, 1)->create(['title' => 'Child A1']);
        MainMenuItem::factory()->childOf($parentA, 2)->create(['title' => 'Child A2']);

        $parentB = MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 2, 'title' => 'Parent B']);

        // Adding a child under parentB — should start at sort_no 1, not 3
        $item = $this->service()->createItem([
            'parent_id' => $parentB->id,
            'title' => 'Child B1',
            'slug' => 'child-b1',
            'is_active' => true,
        ]);

        $this->assertSame(1, $item->sort_no);
    }

    public function test_create_item_persists_to_database(): void
    {
        $this->service()->createItem([
            'parent_id' => 0,
            'title' => 'Persisted',
            'slug' => 'persisted',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('main_menu_items', [
            'title' => 'Persisted',
            'slug' => 'persisted',
            'parent_id' => 0,
            'is_active' => false,
        ]);
    }

    public function test_create_item_returns_main_menu_item_instance(): void
    {
        $item = $this->service()->createItem([
            'parent_id' => 0,
            'title' => 'Item',
            'slug' => 'item',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(MainMenuItem::class, $item);
        $this->assertTrue($item->exists);
    }

    // -------------------------------------------------------------------------
    // buildParentOptionsHtml
    // -------------------------------------------------------------------------

    public function test_build_parent_options_html_includes_root_option(): void
    {
        $nodesMeta = MainMenuItem::newModelInstance()->newCollection();

        $html = $this->service()->buildParentOptionsHtml($nodesMeta);

        // Root option is selected by default (selectedId=0), so attribute may vary;
        // assert by value attribute and label content only.
        $this->assertStringContainsString('value="0"', $html);
        $this->assertStringContainsString('Root', $html);
    }

    public function test_build_parent_options_html_includes_all_items(): void
    {
        $a = MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 1, 'title' => 'Alpha']);
        $b = MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 2, 'title' => 'Beta']);

        $nodesMeta = MainMenuItem::query()->orderBy('sort_no')->get();

        $html = $this->service()->buildParentOptionsHtml($nodesMeta);

        $this->assertStringContainsString('value="'.$a->id.'"', $html);
        $this->assertStringContainsString('value="'.$b->id.'"', $html);
        $this->assertStringContainsString('Alpha', $html);
        $this->assertStringContainsString('Beta', $html);
    }

    public function test_build_parent_options_html_indents_child_items(): void
    {
        $parent = MainMenuItem::factory()->create(['parent_id' => 0, 'sort_no' => 1, 'title' => 'Parent']);
        MainMenuItem::factory()->childOf($parent, 1)->create(['title' => 'Child']);

        $nodesMeta = MainMenuItem::query()->orderBy('sort_no')->get();

        $html = $this->service()->buildParentOptionsHtml($nodesMeta);

        // Child option must contain a dash-indent prefix
        $this->assertMatchesRegularExpression('/option[^>]*>—.*Child/', $html);
    }

    public function test_build_parent_options_html_returns_string(): void
    {
        $nodesMeta = MainMenuItem::newModelInstance()->newCollection();

        $result = $this->service()->buildParentOptionsHtml($nodesMeta);

        $this->assertIsString($result);
    }

    // -------------------------------------------------------------------------
    // stringCastType (via BootstrapTableHelper, exercised through the service)
    // -------------------------------------------------------------------------

    public function test_string_cast_type_returns_text_for_sqlite(): void
    {
        $query = MainMenuItem::query();

        $castType = \App\Helpers\BootstrapTableHelper::stringCastType($query);

        // The test suite always runs on SQLite
        $this->assertSame('TEXT', $castType);
    }
}
