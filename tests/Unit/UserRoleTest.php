<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_is_admin_returns_true_for_administrateur(): void
    {
        $user = new User(['droit' => 'ADMINISTRATEUR']);
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isGestion());
        $this->assertFalse($user->isCollaborateur());
    }

    public function test_is_gestion_returns_true_for_gestionnaire(): void
    {
        $user = new User(['droit' => 'GESTIONNAIRE']);
        $this->assertTrue($user->isGestion());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isCollaborateur());
    }

    public function test_is_collaborateur_returns_true_for_collaborateur(): void
    {
        $user = new User(['droit' => 'COLLABORATEUR']);
        $this->assertTrue($user->isCollaborateur());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isGestion());
    }
}
