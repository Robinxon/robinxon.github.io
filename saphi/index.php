<?php
// Pusta strona PHP
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score Table</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            text-align: center;
            vertical-align: middle;
        }
        
        tbody tr:nth-child(odd) {
            background: #f9f9f9;
        }
        
        tbody tr:nth-child(even) {
            background: #ffffff;
        }
        
        tbody tr.track-odd {
            background: #f9f9f9;
        }
        
        tbody tr.track-even {
            background: #ffffff;
        }
        
        tbody tr:hover {
            background: #f8f9ff !important;
            transition: background 0.3s ease;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .track-name {
            font-weight: 600;
            color: #667eea;
        }
        
        .track-author {
            font-size: 0.9em;
            color: #666;
            font-style: italic;
        }
        
        .board-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .board-link:hover {
            text-decoration: underline;
        }
        
        .driver-icon {
            width: 24px;
            height: 24px;
            vertical-align: middle;
        }
        
        th {
            text-align: center;
        }
        
        th:first-child {
            text-align: left;
        }
        
        .better-time {
            background-color: #d4edda !important;
            color: #155724;
            font-weight: 600;
        }
        
        .worse-time {
            background-color: #f8d7da !important;
            color: #721c24;
            font-weight: 600;
        }
        
        .time-diff {
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .summary {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        
        .summary-item .wins {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
        
        .summary-item .total-diff {
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .summary-item .label {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php
    $url1 = "https://5ever.crashteamranking.com/ctr/pr.php?id=58";
    $url2 = "https://5ever.crashteamranking.com/ctr/pr.php?id=144";

    $data1 = file_get_contents($url1);
    $data2 = file_get_contents($url2);

    $len1 = strlen($data1 ?? '');
    $len2 = strlen($data2 ?? '');

    echo "<script>console.log('data1 length: {$len1}');console.log('data2 length: {$len2}');</script>";

    // Extract table with class scorestable from data1
    preg_match('/<table[^>]*class=["\']scorestable["\'][^>]*>.*?<\/table>/is', $data1, $matches1);
    $data1 = $matches1[0] ?? '';

    // Extract table with class scorestable from data2
    preg_match('/<table[^>]*class=["\']scorestable["\'][^>]*>.*?<\/table>/is', $data2, $matches2);
    $data2 = $matches2[0] ?? '';

    // Combine both tables' data
    $dataAll = $data1 . $data2;

    // Extract all table rows from datas
    preg_match_all('/<script[^>]*>.*?<\/script>/is', $dataAll, $rowMatches);
    $tableRows = $rowMatches[0] ?? [];

    echo "<script>console.log('datas rows: " . count($tableRows) . "');</script>";

    // Extract JSON data from console.log statements
    $extractedData = [];
    foreach ($tableRows as $row) {
        // Extract JSON from console.log(JSON_HERE);
        if (preg_match('/console\.log\((\{.*?\})\);/s', $row, $jsonMatch)) {
            $jsonString = $jsonMatch[1];
            // Decode the JSON
            $decoded = json_decode($jsonString, true);
            if ($decoded !== null) {
                $extractedData[] = $decoded;
            }
        }
    }

    // Sort by TrackID, UserID, and CategoryID
    usort($extractedData, function($a, $b) {
        if ($a['TrackID'] != $b['TrackID']) {
            return $a['TrackID'] <=> $b['TrackID'];
        }
        if ($a['UserID'] != $b['UserID']) {
            return $a['UserID'] <=> $b['UserID'];
        }
        return $a['CategoryID'] <=> $b['CategoryID'];
    });

    // Track names mapping
    $trackNames = [
        1 => 'Drive Thru Danger',
        2 => 'Dreamy Heights',
        3 => 'Breeze Harbor',
        4 => 'Frozen Depths',
        5 => 'Neon Paradise',
        6 => 'Green Hill Raceway',
        7 => 'Yoshi Circuit',
        8 => 'Jungle Boogie N-Gage',
        9 => 'Redrock Ravine'
    ];

    // Group data by TrackID only
    $groupedByTrack = [];
    foreach ($extractedData as $data) {
        $trackID = $data['TrackID'];
        $userID = $data['UserID'];
        
        if (!isset($groupedByTrack[$trackID])) {
            $groupedByTrack[$trackID] = [
                'TrackID' => $trackID,
                'TrackName' => $trackNames[$trackID] ?? 'Unknown Track',
                'users' => []
            ];
        }
        
        if (!isset($groupedByTrack[$trackID]['users'][$userID])) {
            $groupedByTrack[$trackID]['users'][$userID] = [
                'UserID' => $userID,
                'DriverID' => $data['DriverID'],
                'Course' => null,
                'Lap' => null
            ];
        }
        
        if ($data['CategoryID'] == 1) {
            $groupedByTrack[$trackID]['users'][$userID]['Course'] = $data;
        } else if ($data['CategoryID'] == 2) {
            $groupedByTrack[$trackID]['users'][$userID]['Lap'] = $data;
        }
    }
    
    // Function to format time
    function formatTime($cs) {
        if ($cs === null) return '-';
        $minutes = floor($cs / 6000);
        $seconds = floor(($cs % 6000) / 100);
        $centiseconds = $cs % 100;
        if ($minutes > 0) {
            return sprintf('%d:%02d.%02d', $minutes, $seconds, $centiseconds);
        } else {
            return sprintf('%d.%02d', $seconds, $centiseconds);
        }
    }
    
    // Function to calculate and format time difference
    function formatTimeDiff($cs1, $cs2) {
        if ($cs1 === null || $cs2 === null) return '';
        $diff = $cs1 - $cs2;
        if ($diff == 0) return ' (0.00)';
        $sign = $diff > 0 ? '+' : '-';
        $absDiff = abs($diff);
        $seconds = floor($absDiff / 100);
        $centiseconds = $absDiff % 100;
        return sprintf(' (%s%d.%02d)', $sign, $seconds, $centiseconds);
    }

    // Now you can process $extractedData array
    // Example: Display the extracted data
    echo "<script>console.log('Extracted data:');</script>";
    foreach ($extractedData as $index => $data) {
        echo "<script>console.log(" . json_encode($data) . ");</script>";
    }
    
    // Calculate summary statistics
    $robinxonWins = 0;
    $kotekWins = 0;
    $robinxonTotalDiff = 0;
    $kotekTotalDiff = 0;
    
    foreach ($groupedByTrack as $track) {
        $usersList = array_values($track['users']);
        usort($usersList, function($a, $b) {
            return $a['UserID'] <=> $b['UserID'];
        });
        
        $user1 = $usersList[0] ?? null;
        $user2 = $usersList[1] ?? null;
        
        // Course comparison
        $course1CS = $user1 && $user1['Course'] ? $user1['Course']['ScoreCS'] : null;
        $course2CS = $user2 && $user2['Course'] ? $user2['Course']['ScoreCS'] : null;
        
        if ($course1CS !== null && $course2CS !== null) {
            if ($course1CS < $course2CS) {
                $robinxonWins++;
                $kotekTotalDiff += ($course2CS - $course1CS);
            } else if ($course2CS < $course1CS) {
                $kotekWins++;
                $robinxonTotalDiff += ($course1CS - $course2CS);
            }
        } else if ($course1CS !== null) {
            $robinxonWins++;
        } else if ($course2CS !== null) {
            $kotekWins++;
        }
        
        // Lap comparison
        $lap1CS = $user1 && $user1['Lap'] ? $user1['Lap']['ScoreCS'] : null;
        $lap2CS = $user2 && $user2['Lap'] ? $user2['Lap']['ScoreCS'] : null;
        
        if ($lap1CS !== null && $lap2CS !== null) {
            if ($lap1CS < $lap2CS) {
                $robinxonWins++;
                $kotekTotalDiff += ($lap2CS - $lap1CS);
            } else if ($lap2CS < $lap1CS) {
                $kotekWins++;
                $robinxonTotalDiff += ($lap1CS - $lap2CS);
            }
        } else if ($lap1CS !== null) {
            $robinxonWins++;
        } else if ($lap2CS !== null) {
            $kotekWins++;
        }
    }
    
    // Format total differences
    function formatTotalDiff($cs) {
        $seconds = floor($cs / 100);
        $centiseconds = $cs % 100;
        return sprintf('%d.%02d', $seconds, $centiseconds);
    }

    ?>
    
    <div class="container">
        <div class="summary">
            <div class="summary-item">
                <h3>Robinxon</h3>
                <div class="wins"><?php echo $robinxonWins; ?></div>
                <div class="label">lepszych czasów</div>
                <?php if ($robinxonTotalDiff > 0): ?>
                <div class="total-diff" style="color: #721c24;">+<?php echo formatTotalDiff($robinxonTotalDiff); ?>s</div>
                <div class="label">łączna strata</div>
                <?php endif; ?>
            </div>
            
            <div class="summary-item">
                <h3>VS</h3>
            </div>
            
            <div class="summary-item">
                <h3>Magiczny_Kotek</h3>
                <div class="wins"><?php echo $kotekWins; ?></div>
                <div class="label">lepszych czasów</div>
                <?php if ($kotekTotalDiff > 0): ?>
                <div class="total-diff" style="color: #721c24;">+<?php echo formatTotalDiff($kotekTotalDiff); ?>s</div>
                <div class="label">łączna strata</div>
                <?php endif; ?>
            </div>
        </div>
        
        <table>
            <thead>
            <tr>
                <th>Track</th>
                <th>User</th>
                <th>Category</th>
                <th>Driver</th>
                <th>Time</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            $trackIndex = 0;
            foreach ($groupedByTrack as $track): 
                $trackClass = ($trackIndex % 2 == 0) ? 'track-even' : 'track-odd';
                $trackIndex++;
                
                // Get both users' data - ensure we have exactly 2 users
                $usersList = array_values($track['users']);
                
                // Sort users by ID to ensure consistent order (Robinxon first)
                usort($usersList, function($a, $b) {
                    return $a['UserID'] <=> $b['UserID'];
                });
                
                $user1 = $usersList[0] ?? null;
                $user2 = $usersList[1] ?? null;
                
                // Calculate rowspan based on how many users we have
                $totalRows = 0;
                if ($user1) $totalRows += 2;
                if ($user2) $totalRows += 2;
                
                // Course times comparison
                $course1CS = $user1 && $user1['Course'] ? $user1['Course']['ScoreCS'] : null;
                $course2CS = $user2 && $user2['Course'] ? $user2['Course']['ScoreCS'] : null;
                $course1Better = ($course1CS !== null && ($course2CS === null || $course1CS < $course2CS));
                $course2Better = ($course2CS !== null && ($course1CS === null || $course2CS < $course1CS));
                
                // Lap times comparison
                $lap1CS = $user1 && $user1['Lap'] ? $user1['Lap']['ScoreCS'] : null;
                $lap2CS = $user2 && $user2['Lap'] ? $user2['Lap']['ScoreCS'] : null;
                $lap1Better = ($lap1CS !== null && ($lap2CS === null || $lap1CS < $lap2CS));
                $lap2Better = ($lap2CS !== null && ($lap1CS === null || $lap2CS < $lap1CS));
                
                $firstRow = true;
            ?>
                <?php if ($user1): ?>
                <!-- User 1 Course row -->
                <tr class="<?php echo $trackClass; ?>">
                    <?php if ($firstRow): ?>
                    <td rowspan="<?php echo $totalRows; ?>"><?php echo htmlspecialchars($track['TrackName']); ?></td>
                    <?php $firstRow = false; endif; ?>
                    <td rowspan="2"><?php echo htmlspecialchars($user1['UserID'] == 58 ? 'Robinxon' : ($user1['UserID'] == 144 ? 'Magiczny_Kotek' : $user1['UserID'])); ?></td>
                    <td>Course</td>
                    <td><?php if (isset($user1['DriverID'])): ?><img height="24" src='https://5ever.crashteamranking.com/common/p/<?php echo htmlspecialchars($user1['DriverID']); ?>.png'><?php endif; ?></td>
                    <td class="<?php echo $course1Better ? 'better-time' : ($course2Better ? 'worse-time' : ''); ?>">
                        <?php echo formatTime($course1CS) . formatTimeDiff($course1CS, $course2CS); ?>
                    </td>
                    <td><?php echo $user1['Course'] ? htmlspecialchars($user1['Course']['SubmitDate']) : '-'; ?></td>
                </tr>
                
                <!-- User 1 Lap row -->
                <tr class="<?php echo $trackClass; ?>">
                    <td>Lap</td>
                    <td><?php if (isset($user1['DriverID'])): ?><img height="24" src='https://5ever.crashteamranking.com/common/p/<?php echo htmlspecialchars($user1['DriverID']); ?>.png'><?php endif; ?></td>
                    <td class="<?php echo $lap1Better ? 'better-time' : ($lap2Better ? 'worse-time' : ''); ?>">
                        <?php echo formatTime($lap1CS) . formatTimeDiff($lap1CS, $lap2CS); ?>
                    </td>
                    <td><?php echo $user1['Lap'] ? htmlspecialchars($user1['Lap']['SubmitDate']) : '-'; ?></td>
                </tr>
                <?php endif; ?>
                
                <?php if ($user2): ?>
                <!-- User 2 Course row -->
                <tr class="<?php echo $trackClass; ?>">
                    <?php if ($firstRow): ?>
                    <td rowspan="<?php echo $totalRows; ?>"><?php echo htmlspecialchars($track['TrackName']); ?></td>
                    <?php $firstRow = false; endif; ?>
                    <td rowspan="2"><?php echo htmlspecialchars($user2['UserID'] == 58 ? 'Robinxon' : ($user2['UserID'] == 144 ? 'Magiczny_Kotek' : $user2['UserID'])); ?></td>
                    <td>Course</td>
                    <td><?php if (isset($user2['DriverID'])): ?><img height="24" src='https://5ever.crashteamranking.com/common/p/<?php echo htmlspecialchars($user2['DriverID']); ?>.png'><?php endif; ?></td>
                    <td class="<?php echo $course2Better ? 'better-time' : ($course1Better ? 'worse-time' : ''); ?>">
                        <?php echo formatTime($course2CS) . formatTimeDiff($course2CS, $course1CS); ?>
                    </td>
                    <td><?php echo $user2['Course'] ? htmlspecialchars($user2['Course']['SubmitDate']) : '-'; ?></td>
                </tr>
                
                <!-- User 2 Lap row -->
                <tr class="<?php echo $trackClass; ?>">
                    <td>Lap</td>
                    <td><?php if (isset($user2['DriverID'])): ?><img height="24" src='https://5ever.crashteamranking.com/common/p/<?php echo htmlspecialchars($user2['DriverID']); ?>.png'><?php endif; ?></td>
                    <td class="<?php echo $lap2Better ? 'better-time' : ($lap1Better ? 'worse-time' : ''); ?>">
                        <?php echo formatTime($lap2CS) . formatTimeDiff($lap2CS, $lap1CS); ?>
                    </td>
                    <td><?php echo $user2['Lap'] ? htmlspecialchars($user2['Lap']['SubmitDate']) : '-'; ?></td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>