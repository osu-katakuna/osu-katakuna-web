<div class="sidebar">
  @auth
  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
      <img src="{{ env("AVATAR_SERVER") }}/{{ Auth::user()->id }}" class="img-circle elevation-2" alt="{{ Auth::user()->username }}'s profile picture'">
    </div>
    <div class="info">
      <a href="{{ route("user", Auth::user()->id) }}" class="d-block">{{ Auth::user()->username }}</a>
    </div>
  </div>
  @else
  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="info">
      <a href="{{ route("login") }}" class="d-block">Not connected.</a>
    </div>
  </div>
  @endauth

  @auth
  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      @HasPermission("admin.dashboard")
      <li class="nav-item">
        <a href="{{ route("admin") }}" class="nav-link">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>Dashboard</p>
        </a>
      </li>
      @endHasPermission
      @HasPermission("admin.beatmap.manage")
      <li class="nav-item has-treeview @if(Route::is('beatmaps.*')) menu-open @endif">
        <a href="#" class="nav-link @if(Route::is('beatmaps.*')) active @endif">
          <i class="nav-icon fas fa-copy"></i>
          <p>
            Beatmaps
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        @HasPermission("admin.beatmap.manage")
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{ route('beatmaps.manage') }}" class="nav-link @if(Route::is('beatmaps.manage')) active @endif">
              <i class="fas fa-pencil-alt nav-icon"></i>
              <p>Manage</p>
            </a>
          </li>
        </ul>
        @endHasPermission
        @HasPermission("admin.beatmap.manage.add")
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{ route('beatmaps.add') }}" class="nav-link @if(Route::is('beatmaps.add')) active @endif">
              <i class="fas fa-file-upload nav-icon"></i>
              <p>Import</p>
            </a>
          </li>
        </ul>
        @endHasPermission
      </li>
      @endHasPermission
    </ul>
  </nav>
  @endauth
</div>
