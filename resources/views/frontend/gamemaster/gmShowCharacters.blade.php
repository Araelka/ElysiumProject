@extends('frontend.gamemaster.index')
@section('title', 'Персонажи')
@section('table')


<div class="top-menu">
<!-- Левая часть -->
<div class="top-menu-left">
    <form action={{ route('game-master.showCharactersTable') }} method="GET" id="filter-form">
        <button type="submit" name="filter" value="all" class="filter-button {{ request('filter') === 'all' || !request('filter') ? 'active' : '' }}">Все</button>
        @if (auth()->user()->isGameMaster())
            <button type="submit" name="filter" value="approved" class="filter-button {{ request('filter') === 'approved' ? 'active' : '' }}">Одобренные</button>
        @endif
        @if (auth()->user()->isQuestionnaireSpecialist())
            <button type="submit" name="filter" value="consideration" class="filter-button {{ request('filter') === 'consideration' ? 'active' : '' }}">На рассмотрении</button>
            <button type="submit" name="filter" value="preparing" class="filter-button {{ request('filter') === 'preparing' ? 'active' : '' }}">В работе</button>
            <button type="submit" name="filter" value="rejected" class="filter-button {{ request('filter') === 'rejected' ? 'active' : '' }}">Отклонённые</button>
        @endif
        @if (auth()->user()->isGameMaster())
            <button type="submit" name="filter" value="archive" class="filter-button {{ request('filter') === 'archive' ? 'active' : '' }}">В архиве</button>
            <button type="submit" name="filter" value="dead" class="filter-button {{ request('filter') === 'dead' ? 'active' : '' }}">Мёртвые</button>
        @endif
    </form>
</div>

</div>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Игрок</th>
                <th>Имя Фамилия</th>
                <th>Статус</th>
                @if (auth()->user()->isGameMaster() && request('filter') === 'approved')
                    <th>Свободных очков</th>
                @endif
                @if (auth()->user()->isQuestionnaireSpecialist() && request('filter') === 'rejected')
                    <th>Причина отклонения</th>
                @endif
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
        @isset($characters)
            @foreach ($characters as $character)
            <tr>
                <td>{{ $character->user->login }}</td>
                <td>{{ $character->firstName . ' ' . $character->secondName }}</td>
                <td>{{ $character->status->name }}</td>
                @if (auth()->user()->isGameMaster() && request('filter') === 'approved')
                    <td>{{ $character->getAvailablePoints() }}</td>
                @endif
                @if (auth()->user()->isQuestionnaireSpecialist() && request('filter') === 'rejected')
                    <td>{{ Str::limit($character->comment, 50) }}</td>
                @endif
                <td>
                    <a href={{ route('game-master.showCharacter', $character->id) }} class="edit-button">Посмотреть</a>
                </td>
            </tr>
            @endforeach
        @endisset
        </tbody>
    </table>
</div>
<div class="pagination-container">
    {{ $characters->links('vendor.pagination.default') }}
</div>
@endsection
