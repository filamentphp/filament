window.getCookie = (name) => {
  let v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)')
  return v ? v[2] : null
}

window.setCookie = (name, value, days = 365) => {
  let d = new Date()
  d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * days)
  document.cookie = name + '=' + value + ';path=/;expires=' + d.toGMTString()
}

window.deleteCookie = (name) => {
  setCookie(name, '', -1)
}
