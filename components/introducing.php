<div class="w-full flex justify-center items-center mt-20 mb-8.5">
    <span class="font-[550]">Generation Haseki</span>
</div>
<div class="grid grid-cols-2 bg-blue-500">


    <div class="w-full h-full relative grid group overflow-hidden reveal-section">
        <img src="../asset/introducing1.png" class="w-full h-full col-start-1 row-start-1 object-cover transition-all duration-150 group-hover:brightness-80 group-hover:scale-110">
        <div class="sticky bottom-0 flex justify-center items-center flex-col gap-3 col-start-1 row-start-1 py-10">
            <p class="text-white font-[550] opacity-0 translate-y-10 transition-all duration-1000 delay-100 reveal-content">Introducing Fashion Men's Generation</p>
            <button class="text-white border border-white bg-black/50 px-5 py-3 rounded-md transition-all hover:bg-white hover:text-black hover:font-bold opacity-0 translate-y-10 duration-100 reveal-content">Shop Now</button>
        </div>
    </div>


    <div class="w-full h-full relative grid group overflow-hidden reveal-section">
        <img src="../asset/introducing3.jpg" class="w-full h-full col-start-1 row-start-1 object-cover transition-all duration-150 group-hover:brightness-80 group-hover:scale-110">
        <div class="sticky bottom-0 flex justify-center items-center flex-col gap-3 col-start-1 row-start-1 py-10">
            <p class="text-white font-[550] opacity-0 translate-y-10 transition-all duration-1000 delay-100 reveal-content">Introducing Fashion Women's Generation</p>
            <button class="text-white border border-white bg-black/50 px-5 py-3 rounded-md transition-all hover:bg-white hover:text-black hover:font-bold opacity-0 translate-y-10 duration-100 reveal-content">Shop Now</button>
        </div>
    </div>


</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.15 // Kích hoạt khi 15% banner xuất hiện
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Tìm tất cả các phần tử nội dung bên trong section này
                    const elements = entry.target.querySelectorAll('.reveal-content');
                    elements.forEach(el => {
                        el.classList.remove('opacity-0', 'translate-y-10');
                        el.classList.add('opacity-100', 'translate-y-0');
                    });
                    // Sau khi chạy hiệu ứng một lần, có thể ngừng quan sát để tiết kiệm tài nguyên
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Quan sát cả hai khối banner
        document.querySelectorAll('.reveal-section').forEach(section => {
            observer.observe(section);
        });
    });
</script>