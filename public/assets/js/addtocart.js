$(document).ready(function () {
    // Session data initialization
    const jenisPesanan = $('#session-jenis').val();
    const mejaId = $('#session-meja').val();
    const nomorWa = $('#session-wa').val();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Debug log untuk memastikan session data tersedia
    if (jenisPesanan || mejaId || nomorWa) {
        console.log('Session Data:', {
            jenisPesanan: jenisPesanan,
            mejaId: mejaId,
            nomorWa: nomorWa
        });
    }

    /**
     * Format angka ke Rupiah
     */
    function formatRupiah(amount) {
        const parsedAmount = parseInt(amount);
        if (isNaN(parsedAmount)) {
            console.error('Invalid amount provided to formatRupiah:', amount);
            return 'Rp 0';
        }
        return 'Rp ' + parsedAmount.toLocaleString('id-ID');
    }

    /**
     * Update badge cart count - DIPERBAIKI: Kembali ke implementasi sederhana yang bekerja
     */
    function updateCartBadge(count) {
        const cartBadge = $('#cart-count');
        if (cartBadge.length) {
            cartBadge.text(count || 0);
            if (count > 0) {
                cartBadge.removeClass('d-none').addClass('badge bg-primary');
            } else {
                cartBadge.addClass('d-none');
            }
        }
    }

    /**
     * Get cart data from localStorage
     */
    function getCartFromLocalStorage() {
        try {
            const cartData = localStorage.getItem('cart');
            return cartData ? JSON.parse(cartData) : [];
        } catch (e) {
            console.warn('Error parsing cart from localStorage:', e);
            return [];
        }
    }

    /**
     * Sync cart to localStorage
     */
    function syncCartToLocalStorage(cartData) {
        try {
            if (cartData && Array.isArray(cartData)) {
                localStorage.setItem('cart', JSON.stringify(cartData));
            }
        } catch (e) {
            console.warn('Error saving cart to localStorage:', e);
        }
    }

    /**
     * Get folder from category
     */
    function getFolderFromCategory(kategori) {
        const folderMap = {
            'Makanan': 'makanan',
            'Minuman': 'minuman',
            'Nasi dan Mie': 'nasi-dan-mie',
            'Aneka Snack': 'aneka-snack'
        };
        return folderMap[kategori] || 'default';
    }

    /**
     * Enhanced recommendation popup with better UI and cross-category recommendations
     */
    function showRecommendationPopup(menuId) {
        console.log('Showing recommendation popup for menu:', menuId);
        
        // Show loading state dengan SweetAlert2
        Swal.fire({
            title: 'Mencari Rekomendasi',
            html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Mencari rekomendasi menu terbaik untuk Anda...</p>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const currentCart = getCartFromLocalStorage();
        const selectedMenus = currentCart.map(item => item.id);
        
        // Add current menu ID
        if (!selectedMenus.includes(parseInt(menuId))) {
            selectedMenus.push(parseInt(menuId));
        }
        
        console.log('Selected menus for recommendation:', selectedMenus);
        
        // Gunakan route yang fleksibel
        const recommendationUrl = $('#recommendation-route').val() || '/dinein/rekomendasi/get';
        
        // AJAX request untuk rekomendasi
        $.ajax({
            url: recommendationUrl,
            method: 'POST',
            dataType: 'json',
            timeout: 15000,
            data: {
                selected_menus: selectedMenus,
                current_menu_id: parseInt(menuId),
                _token: csrfToken
            },
            success: function(response) {
                console.log('Recommendation response:', response);
                
                if (response.status && response.recommendations && response.recommendations.length > 0) {
                    displayRecommendations(response);
                } else {
                    displayNoRecommendations(response.message || 'Belum ada rekomendasi yang cocok saat ini.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching recommendations:', { status, error, responseText: xhr.responseText });
                
                let errorMessage = 'Terjadi kesalahan sistem saat memuat rekomendasi.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse?.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Use default message
                }
                
                displayErrorRecommendations(errorMessage);
            }
        });
    }

    /**
     * Display recommendations with enhanced UI
     */
    function displayRecommendations(data) {
        const algorithmUsed = data.algorithm_used || 'unknown';
        
        let algorithmInfo = '';
        let algorithmColor = '#28a745';
        let algorithmIcon = 'fas fa-lightbulb';
        
        switch (algorithmUsed) {
            case 'apriori':
                algorithmInfo = 'Rekomendasi berdasarkan Algoritma Apriori - Analisis pola pembelian pelanggan';
                algorithmColor = '#007bff';
                algorithmIcon = 'fas fa-brain';
                break;
            case 'intelligent_fallback':
                algorithmInfo = 'Rekomendasi cerdas berdasarkan kategori dan popularitas menu';
                algorithmColor = '#17a2b8';
                algorithmIcon = 'fas fa-star';
                break;
            default:
                algorithmInfo = 'Rekomendasi berdasarkan menu populer';
                algorithmColor = '#28a745';
                algorithmIcon = 'fas fa-thumbs-up';
        }
        
        let recommendationsHtml = `
            <div class="recommendation-header text-center mb-4">
                <h3 class="mb-2" style="color: ${algorithmColor};">
                    <i class="${algorithmIcon} me-2"></i>
                    Menu Yang Mungkin Anda Suka
                </h3>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    ${algorithmInfo}
                </p>
            </div>
            <div class="row justify-content-center">
        `;
        
        data.recommendations.forEach(function(item, index) {
            const folder = getFolderFromCategory(item.kategori);
            const imagePath = `/assets/img/${folder}/${item.gambar || 'default.png'}`;
            const formattedPrice = new Intl.NumberFormat('id-ID').format(item.harga);
            
            // Confidence badge color
            let confidenceClass = 'bg-success';
            if (item.confidence < 60) {
                confidenceClass = 'bg-warning';
            } else if (item.confidence < 80) {
                confidenceClass = 'bg-info';
            }
            
            recommendationsHtml += `
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="recommendation-card card h-100 shadow-sm" data-id="${item.id}" 
                         style="border-radius: 15px; overflow: hidden; transition: all 0.3s ease;">
                        <div class="position-relative">
                            <img src="${imagePath}" 
                                 class="card-img-top" 
                                 alt="${item.nama_menu}" 
                                 style="height: 150px; object-fit: cover;"
                                 onerror="this.src='/assets/img/default.png';">
                            ${item.confidence ? `
                                <span class="position-absolute top-0 end-0 badge ${confidenceClass} m-2" 
                                      style="font-size: 0.7rem;">
                                    ${Math.round(item.confidence)}% match
                                </span>
                            ` : ''}
                        </div>
                        <div class="card-body text-center d-flex flex-column">
                            <h6 class="card-title fw-bold mb-2" style="min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                ${item.nama_menu}
                            </h6>
                            <div class="badge bg-secondary text-white mb-2" style="font-size: 0.75rem;">
                                ${item.kategori}
                            </div>
                            <p class="card-text fw-bold text-success mb-2" style="font-size: 1.1rem;">
                                Rp. ${formattedPrice}
                            </p>
                            <p class="text-muted small mb-3" style="font-style: italic; min-height: 36px;">
                                ${item.rule_text || 'Rekomendasi spesial untuk Anda'}
                            </p>
                            <button class="btn btn-primary recommendation-add-btn mt-auto w-100" 
                                    data-id="${item.id}" 
                                    data-name="${item.nama_menu}" 
                                    data-price="${item.harga}"
                                    style="border-radius: 25px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-cart-plus me-2"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        recommendationsHtml += `</div>`;
        
        // Show recommendations with SweetAlert2
        Swal.fire({
            title: false,
            html: recommendationsHtml,
            width: '900px',
            padding: '2rem',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'recommendation-popup-custom',
                closeButton: 'recommendation-close-btn'
            },
            didOpen: () => {
                // Bind recommendation button events
                bindRecommendationEvents();
                
                // Add hover effects
                $('.recommendation-card').hover(
                    function() {
                        $(this).css('transform', 'translateY(-5px)');
                        $(this).find('.card-img-top').css('transform', 'scale(1.05)');
                    },
                    function() {
                        $(this).css('transform', 'translateY(0)');
                        $(this).find('.card-img-top').css('transform', 'scale(1)');
                    }
                );
            }
        });
    }

    /**
     * Display message when no recommendations available
     */
    function displayNoRecommendations(message) {
        Swal.fire({
            title: 'Belum Ada Rekomendasi',
            html: `
                <div class="text-center p-4">
                    <i class="fas fa-utensils" style="font-size: 48px; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <h4 class="text-muted mb-2">Menu Sudah Lengkap!</h4>
                    <p class="text-secondary">${message}</p>
                    <p class="text-muted small">Silakan lanjutkan ke checkout atau pilih menu lainnya.</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff',
            width: '40rem',
            padding: '2rem',
            customClass: { popup: 'custom-swal-popup' }
        });
    }

    /**
     * Display error message
     */
    function displayErrorRecommendations(message) {
        Swal.fire({
            title: 'Terjadi Kesalahan',
            html: `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #e74c3c; margin-bottom: 20px;"></i>
                    <h4 class="text-danger mb-2">Gagal Memuat Rekomendasi</h4>
                    <p class="text-secondary">${message}</p>
                    <p class="text-muted small">Anda masih dapat melanjutkan pemesanan tanpa rekomendasi.</p>
                </div>
            `,
            icon: 'error',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#dc3545',
            width: '40rem',
            padding: '2rem',
            customClass: { popup: 'custom-swal-popup' }
        });
    }

    /**
     * Add recommended item to cart
     */
    function addRecommendedToCart(menuId, menuName, menuPrice) {
        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }

        const postData = {
            _token: csrfToken,
            menu_id: menuId,
            jenis_pesanan: jenisPesanan,
            qty: 1
        };
        
        if (jenisPesanan === 'dinein') {
            postData.meja_id = mejaId;
        }
        
        const requestUrl = jenisPesanan === 'dinein' ? '/dinein/store' : '/takeaway/store';
        const buttonElement = $(`.recommendation-add-btn[data-id="${menuId}"]`);
        const originalButtonHtml = buttonElement.html();
        
        // Update button state
        buttonElement.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menambah...');

        $.ajax({
            url: requestUrl,
            method: 'POST',
            dataType: 'json',
            data: postData,
            success: function (response) {
                console.log('Add recommended item response:', response);
                
                if (response.success) {
                    // Update UI
                    updateUI(response);
                    
                    // Show success feedback on button
                    buttonElement.html('<i class="fas fa-check me-2"></i>Ditambahkan!')
                                .removeClass('btn-primary')
                                .addClass('btn-success');
                    
                    // Show toast notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: `${menuName} berhasil ditambahkan!`
                    });
                    
                    // Close recommendation popup after 2 seconds
                    setTimeout(() => {
                        Swal.close();
                    }, 2000);
                    
                } else {
                    buttonElement.prop('disabled', false).html(originalButtonHtml);
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal Menambahkan',
                        text: response.message || 'Menu tidak dapat ditambahkan ke keranjang',
                        confirmButtonColor: '#f44336',
                        width: '40rem',
                        padding: '2rem',
                        customClass: { popup: 'custom-swal-popup' }
                    });
                }
            },
            error: function (xhr) {
                console.error('Error adding recommended item:', xhr);
                buttonElement.prop('disabled', false).html(originalButtonHtml);
                
                handleCartError(xhr, 'Gagal menambahkan menu rekomendasi ke keranjang.');
            }
        });
    }

    /**
     * Handle cart response - Menggunakan logika dari kode lama yang sudah terbukti bekerja
     */
    function handleCartResponse(response, menuName = null) {
        if (response.success) {
            updateUI(response);
            if (menuName) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `${menuName} berhasil ditambahkan ke keranjang.`,
                    timer: 1500,
                    showConfirmButton: false,
                    width: '40rem',
                    padding: '2rem',
                    customClass: { popup: 'custom-swal-popup' }
                });
            }
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Gagal',
                text: response.message || 'Item tidak dapat ditambahkan.',
                width: '40rem',
                padding: '2rem',
                customClass: { popup: 'custom-swal-popup' }
            });
        }
    }

    /**
     * Handle AJAX errors
     */
    function handleCartError(xhr, defaultMessage) {
        console.error('AJAX Error:', xhr);
        let errorMessage = defaultMessage;
        try {
            const errorResponse = JSON.parse(xhr.responseText);
            if (errorResponse.message) {
                errorMessage = errorResponse.message;
            }
        } catch (e) {}
        
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: errorMessage,
            width: '40rem',
            padding: '2rem',
            customClass: { popup: 'custom-swal-popup' }
        });
    }

    /**
     * Update UI components - Menggunakan logika dari kode lama yang sudah terbukti bekerja
     */
    function updateUI(response) {
        if (response.cart_html) {
            $('#cart-items').html(response.cart_html);
        }
        if (response.order_summary) {
            $('#order-summary').html(response.order_summary);
        }
        if (response.cart_count !== undefined) {
            updateCartBadge(response.cart_count);
        }
        if (response.total !== undefined && $('#totalPrice').length) {
            $('#totalPrice').text(formatRupiah(response.total));
        }
        if (response.cart_data) {
            syncCartToLocalStorage(response.cart_data);
        }
    }

    /**
     * Validate session data
     */
    function validateSession() {
        if (!jenisPesanan) {
            return { valid: false, text: 'Jenis pesanan belum dipilih. Silakan pilih Dine-In atau Takeaway.' };
        }
        if (jenisPesanan === 'dinein' && !mejaId) {
            return { valid: false, text: 'ID Meja belum dipilih. Silakan lengkapi data booking.' };
        }
        if (jenisPesanan === 'takeaway' && !nomorWa) {
            return { valid: false, text: 'Nomor WhatsApp belum diisi. Silakan lengkapi data pelanggan.' };
        }
        return { valid: true, text: '' };
    }

    /**
     * Show validation warning
     */
    function showValidationWarning(text) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: text,
            width: '40rem',
            padding: '2rem',
            customClass: { popup: 'custom-swal-popup' }
        });
    }

    /**
     * Bind recommendation button events
     */
    function bindRecommendationEvents() {
        $('.recommendation-add-btn').off('click').on('click', function(e) {
            e.preventDefault();
            const menuId = $(this).data('id');
            const menuName = $(this).data('name');
            const menuPrice = $(this).data('price');
            addRecommendedToCart(menuId, menuName, menuPrice);
        });
    }

    // Konfigurasi SweetAlert yang umum - Dari kode lama
    const swalConfig = {
        width: '40rem',
        padding: '2rem',
        customClass: { popup: 'custom-swal-popup' }
    };

    // ===== MAIN EVENT HANDLERS =====

    // Add to cart button handler
    $(document).on('click', '.add-to-cart-btn', function (e) {
        e.preventDefault();

        const menuId = $(this).data('id');
        const menuName = $(this).data('name');
        
        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }

        const postData = {
            _token: csrfToken,
            menu_id: menuId,
            jenis_pesanan: jenisPesanan,
            qty: 1
        };
        
        if (jenisPesanan === 'dinein') {
            postData.meja_id = mejaId;
        }

        const requestUrl = jenisPesanan === 'dinein' ? '/dinein/store' : '/takeaway/store';
        const buttonElement = $(this);
        buttonElement.prop('disabled', true).text('Menambah...');

        $.ajax({
            url: requestUrl,
            method: 'POST',
            dataType: 'json',
            data: postData,
            success: function (response) {
                console.log('Add to cart response:', response);
                handleCartResponse(response, menuName);
                
                if (response.success) {
                    // Show recommendation popup after successful add
                    setTimeout(() => {
                        showRecommendationPopup(menuId);
                    }, 300);
                }
            },
            error: function (xhr) {
                handleCartError(xhr, 'Gagal menambahkan item ke keranjang.');
            },
            complete: function() {
                buttonElement.prop('disabled', false).text('Tambah ke Keranjang');
            }
        });
    });

    // Update quantity handlers
    $(document).on('click', '.btn-qty, .update-qty', function (e) {
        e.preventDefault();
        
        const cartId = $(this).data('cart-id') || $(this).data('id');
        const action = $(this).data('action');

        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }

        if (!cartId || !['increase', 'decrease'].includes(action)) {
            Swal.fire({ 
                icon: 'error', 
                title: 'Error', 
                text: 'Data tidak valid.',
                ...swalConfig
            });
            return;
        }

        $(this).prop('disabled', true);
        const updateUrl = jenisPesanan === 'dinein' ? '/dinein/update' : '/takeaway/update';

        $.ajax({
            url: updateUrl,
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId, action: action, _token: csrfToken },
            success: function (response) {
                if (response.success) {
                    updateUI(response);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: response.message || 'Tidak bisa memperbarui jumlah item.',
                        ...swalConfig
                    });
                }
            },
            error: function (xhr) {
                handleCartError(xhr, 'Gagal memperbarui item.');
            },
            complete: function() {
                $('.btn-qty, .update-qty').prop('disabled', false);
            }
        });
    });

    // Delete item handlers
    $(document).on('click', '.btn-delete-cart, .delete-cart', function (e) {
        e.preventDefault();
        
        const cartId = $(this).data('cart-id') || $(this).data('id');
        
        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }
        
        if (!cartId) {
            Swal.fire({ 
                icon: 'error', 
                title: 'Error', 
                text: 'Cart ID tidak ditemukan.',
                ...swalConfig
            });
            return;
        }

        const destroyUrl = jenisPesanan === 'dinein' ? `/dinein/destroy/${cartId}` : `/takeaway/destroy/${cartId}`;

        Swal.fire({
            title: 'Yakin ingin menghapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            ...swalConfig
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus item...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: destroyUrl,
                    method: 'DELETE',
                    dataType: 'json',
                    data: { _token: csrfToken },
                    success: function (response) {
                        if (response.success) {
                            updateUI(response);
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: 'Item berhasil dihapus dari keranjang.',
                                timer: 2000,
                                showConfirmButton: false,
                                ...swalConfig
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal',
                                text: response.message || 'Gagal menghapus item.',
                                ...swalConfig
                            });
                        }
                    },
                    error: function (xhr) {
                        handleCartError(xhr, 'Gagal menghapus item dari keranjang.');
                    }
                });
            }
        });
    });

    // Initialize cart UI on page load - DIPERBAIKI: Kembali ke implementasi kode lama
    (function initializeCartUI() {
        const cart = getCartFromLocalStorage();
        updateCartBadge(cart.length);
    })();

    // Global functions for external access
    window.showRecommendationPopup = showRecommendationPopup;
    window.addRecommendedToCart = addRecommendedToCart;
    window.updateCartBadge = updateCartBadge;
});