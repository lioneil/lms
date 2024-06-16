<?php

namespace Material\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Material\Models\Material;
use Material\Services\MaterialServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Material\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PublishMaterialsTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(MaterialServiceInterface::class);

    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.publish
     * @group  user:materials.publish
     * @return void
     */
    public function a_user_can_publish_a_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.publish']));
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();

        // dd($material);

        // Actions
        $response = $this->post(route('api.materials.publish', $material->getKey()));
        // dd($response);

        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($material->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.publish
     * @group  user:materials.publish
     * @return void
     */
    public function a_user_cannot_publish_others_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.publish']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('api.materials.publish', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($material->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.unpublish
     * @group  user:materials.unpublish
     * @return void
     */
    public function a_user_can_unpublish_a_published_owned_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.unpublish']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // dd($material);
        $material->publish();

        // Actions
        $response = $this->post(route('api.materials.unpublish', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($material->isUnpublished());
        $this->assertFalse($material->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.unpublish
     * @group  user:materials.unpublish
     * @return void
     */
    public function a_user_cannot_unpublish_others_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.unpublish']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create()->random();
        $material->publish();

        // Actions
        $response = $this->post(route('api.materials.unpublish', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($material->isUnpublished());
        $this->assertTrue($material->isPublished());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.draft
     * @group  user:materials.draft
     * @return void
     */
    public function a_user_can_draft_owned_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.draft']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->post(route('api.materials.draft', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($material->isPublished());
        $this->assertTrue($material->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.draft
     * @group  user:materials.draft
     * @return void
     */
    public function a_user_cannot_draft_others_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.draft']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 2)->create()->random();

        // Actions
        $response = $this->post(route('api.materials.draft', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($material->isDrafted());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.expire
     * @group  user:materials.expire
     * @return void
     */
    public function a_user_can_expire_owned_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.expire']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 3)->create([
            'user_id' => $user->getKey()
        ])->random();

        // Actions
        $response = $this->patch(route('api.materials.expire', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($material->isExpired());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:material
     * @group  materials.expire
     * @group  user:materials.expire
     * @return void
     */
    public function a_user_cannot_expire_others_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.expire']));
        $this->withPermissionsPolicy();
        $material = factory(Material::class, 2)->create()->random();

        // Actions
        $response = $this->patch(route('api.materials.expire', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertFalse($material->isExpired());
    }
}
