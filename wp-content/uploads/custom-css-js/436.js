<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
function give_qr() {
var x = Math.floor((Math.random() * 99) + 1);
console.log(x);
document.write("<img src='https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" + x + "'>");
}</script>
<!-- end Simple Custom CSS and JS -->
