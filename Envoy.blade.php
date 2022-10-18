@servers(['production' => 'deployer_user@server_ip'])

@setup
    $repository = 'git@gitlab.com:example_repository.git';
    $releases_dir = '/home/deployer_user/example_repository/releases';
    $current_dir = $app_dir . '/current';
    $app_dir = '/home/deployer_user/example_repository';
    $release = date('Y-m-d\TH:i:s');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    update_symlinks
@endstory

@task('clone_repository')
    echo 'Cloning repository {{ $repository }} ...'
    [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
    git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
    cd {{ $new_release_dir }}
    git reset --hard {{ $commit }}
@endtask

@task('run_composer')
    echo "Install composer dependencies ..."
    cd {{ $new_release_dir }}
    composer install --prefer-dist --no-dev -o
@endtask

@task('update_symlinks')
    echo "Linking storage directory ..."
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage

    echo 'Linking .env file ...'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env

    echo 'Linking current release ...'
    ln -nfs {{ $new_release_dir }} {{ $current_dir }}
@endtask


