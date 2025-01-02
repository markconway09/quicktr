<style>
#custom-menu {
  position: absolute;
  background-color: white;
  border: 1px solid #ccc;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  display: none;
}
#custom-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
#custom-menu li {
  padding: 8px 12px;
  cursor: pointer;
}
#custom-menu li:hover {
  background-color: #f0f0f0;
}
.hidden {
  display: none;
}
</style>

<div id="custom-menu" class="hidden">
  <ul>
    <li onclick="handleOption('Option 1')">Option 1</li>
    <li onclick="handleOption('Option 2')">Option 2</li>
    <li onclick="handleOption('Option 3')">Option 3</li>
  </ul>
</div>

<script>
    const customMenu = document.getElementById('custom-menu');

// Show custom menu
document.addEventListener('contextmenu', (e) => {
  e.preventDefault(); // Prevent the default context menu
  customMenu.style.left = `${e.pageX}px`;
  customMenu.style.top = `${e.pageY}px`;
  customMenu.style.display = 'block';
});

// Hide custom menu on click elsewhere
document.addEventListener('click', () => {
  customMenu.style.display = 'none';
});

// Example handler for menu options
function handleOption(option) {
  alert(`You selected: ${option}`);
  customMenu.style.display = 'none';
}

</script>