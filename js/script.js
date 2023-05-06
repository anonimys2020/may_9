function set_cookie(id) {
  document.cookie = "current_id=" + id;
  location.reload();
}