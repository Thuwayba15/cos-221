<?php include 'header.php'; ?>

</header>
    <main>
    <link rel="stylesheet" href="css/recommendations.css">
        <section id="recommendations">
            <h1>RECOMMENDATIONS</h1>
            <div class="filters">
                <label for="genre">GENRE</label>
                <select id="genre">
                    <!-- Options will be populated dynamically -->
                </select>

                <label for="sort-ratings">SORT BY RATINGS</label>
                <select id="sort-ratings">
                    <option value="DESC">Descending</option>
                </select>

                <label for="sort-release">SORT BY RELEASE DATE</label>
                <select id="sort-release">
                    <option value="ASC">Ascending</option>
                    <option value="DESC">Descending</option>
                </select>

                <label for="sort-title">SORT BY TITLE</label>
                <select id="sort-title">
                    <option value="ASC">Ascending</option>
                    <option value="DESC">Descending</option>
                </select>
            </div>
            <div class="recommendations-container">
                <!-- Recommendations will be populated dynamically -->
            </div>
        </section>
        <script src="js/recommendations.js"></script>
    </main>
    <?php include 'footer.php'; ?>