
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<h2>Quiz Scores from Latest Sessions</h2>
    <?php 
    echo "
        <canvas id='quizScoreHistory'></canvas>
        <script>
            new Chart(document.getElementById('quizScoreHistory'), {
                type: 'line',
                data: {
                    labels: new Array(10).fill(''),
                    datasets: [{
                        label: 'Quiz Score',
                        data: [100, 50, 80, 70, 20, 100, 50, 80, 70, 20]
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        </script>
    "; ?>