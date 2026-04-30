<?php
// views/templates/footer.php
?>
</main>
<footer>
    <p>Copyright &copy; Detail Lab<br></p>
    <div id="date">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var date = document.lastModified;
                document.getElementById("date").textContent = "Last Modified: " + date;
            });
        </script>
    </div>
</footer>
</div>
</body>
</html>