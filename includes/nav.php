<?php
$navItems = [
    ['icon' => 'fa-home', 'label' => 'হোম', 'page' => 'index.php'],
    ['icon' => 'fa-history', 'label' => 'পূর্বের হিসাব', 'page' => 'history.php'],
    ['icon' => 'fa-book', 'label' => 'বাকি', 'page' => 'due.php'],
    ['icon' => 'fa-chart-pie', 'label' => 'রিপোর্ট', 'page' => 'report.php']
];

$activePage = basename($_SERVER['PHP_SELF']);
?>

<div class="bg-white border-t p-3 flex justify-around items-center fixed bottom-0 w-full max-w-[480px] text-gray-400 z-50">
    <?php foreach ($navItems as $item): ?>
        <?php 
            $isActive = $activePage === $item['page'];
            $colorClass = $isActive ? 'text-blue-600' : 'hover:text-blue-600 transition';
        ?>
        <button onclick="window.location.href='<?php echo $item['page']; ?>'" class="flex flex-col items-center <?php echo $colorClass; ?>">
            <i class="fas <?php echo $item['icon']; ?> text-xl"></i>
            <span class="text-[10px] mt-1"><?php echo $item['label']; ?></span>
        </button>
    <?php endforeach; ?>
</div>
