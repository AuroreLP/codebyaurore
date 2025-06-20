#!/bin/bash
set -eo pipefail

# Fonction pour changer ownership seulement si le dossier n'est pas un volume bind monté
safe_chown() {
  local target_dir="$1"
  local owner="$2"
  
  if mountpoint -q "$target_dir"; then
    echo "$target_dir est un volume monté, skipping chown"
  else
    echo "Attribution de $owner sur $target_dir"
    find "$target_dir" ! -path "${target_dir}/db_data*" -exec chown $owner {} + || true
  fi
}

  echo "Mode production : attribution de www-data"
  safe_chown "/var/www/html/var" "www-data:www-data"

# Démarre Apache en foreground
exec apache2-foreground

