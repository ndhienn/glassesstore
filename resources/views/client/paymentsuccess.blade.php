

@section('title', $success ? 'Thanh toán thành công' : 'Thanh toán thất bại')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center {{ $success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} px-4 py-12">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 {{ $success ? 'text-green-500' : 'text-red-500' }} mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        @if ($success)
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2l4 -4m5 2a9 9 0 11-18 0a9 9 0 0118 0z" />
        @else
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M6 18L18 6M6 6l12 12" />
        @endif
    </svg>

    <h1 class="text-3xl font-bold mb-2">
        {{ $success ? 'Thanh toán thành công!' : 'Thanh toán thất bại!' }}
    </h1>

    <p class="text-lg mb-6">
        {{ $success ? 'Cảm ơn bạn đã mua hàng. Chúng tôi sẽ xử lý đơn hàng sớm.' : 'Đơn hàng chưa được thanh toán. Vui lòng thử lại.' }}
    </p>

    <a href="/" class="bg-{{ $success ? 'green' : 'red' }}-600 hover:bg-{{ $success ? 'green' : 'red' }}-700 text-white font-bold py-2 px-4 rounded">
    Quay về trang chủ
    </a>
</div>
@endsection
