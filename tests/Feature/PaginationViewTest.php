<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginationViewTest extends TestCase
{
    public function test_pagination_shows_at_most_five_page_numbers(): void
    {
        $paginator = new LengthAwarePaginator(
            collect(range(1, 10)),
            210,
            10,
            10
        );

        $html = $paginator
            ->links('components.pagination.limited')
            ->render();

        preg_match_all(
            '/class="page-link"[^>]*>(\d+)<\/(?:a|span)>/',
            $html,
            $matches
        );

        $this->assertSame([8, 9, 10, 11, 12], array_map('intval', $matches[1]));
    }
}
