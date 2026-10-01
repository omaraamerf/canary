{{-- Applies a saved light/dark choice before first paint; without one the OS preference applies. --}}
<script>try{var t=localStorage.getItem('theme');if(t==='light'||t==='dark')document.documentElement.dataset.theme=t}catch(e){}</script>
