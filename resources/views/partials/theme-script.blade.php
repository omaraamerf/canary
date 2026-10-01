{{-- Runs before first paint: marks JS as available and applies a saved light/dark choice (otherwise the OS preference applies). --}}
<script>document.documentElement.classList.add('js');try{var t=localStorage.getItem('theme');if(t==='light'||t==='dark')document.documentElement.dataset.theme=t}catch(e){}</script>
