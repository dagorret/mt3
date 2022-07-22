path=$(printf '%s\n' "${PWD##*/}")
command="docker exec ${path}-laravel.test-1 php "$@""
echo "Running php on docker ${path}-laravel.test-1"
$command
