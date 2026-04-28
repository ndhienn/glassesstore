{/* <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('refreshBtn').addEventListener('click', function () {
            // Làm sạch ô tìm kiếm và dropdown lọc
            const searchInput = document.getElementById('keyword');
            if (searchInput) searchInput.value = '';

            const quyenSelect = document.querySelector('select[name="keywordQuyen"]');
            if (quyenSelect) quyenSelect.selectedIndex = 0;

            // Xoá tất cả query params và reload trang
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('keyword');
            currentUrl.searchParams.delete('keywordQuyen');
            currentUrl.searchParams.delete('page');
            window.location.href = currentUrl.toString();
        });
        const searchForm = document.querySelector('form[role="search"]');
        
        if (searchForm) {
            searchForm.addEventListener('submit', function (e) {
                const keywordInput = document.getElementById('keyword');
                
                // Nếu người dùng đã nhập tìm kiếm, sẽ không cần reset keyword nữa
                if (keywordInput.value.trim()) {
                    return;
                }

                // Nếu người dùng không nhập gì, xóa keyword trong URL trước khi tìm kiếm theo quyền
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('keyword'); // Xóa 'keyword' khỏi URL
                window.location.href = currentUrl.toString();
            });
        }
    });
</script> */}