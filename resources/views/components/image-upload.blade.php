@props(['label', 'name', 'image' => null, 'id'])

<div class="form-group">
    <label>{{ $label }}</label>
    <div class="image-upload">
        <div class="thumb">
            <div class="avatar-preview">
                <div class="profilePicPreview" id="imagePreview{{ $id }}"
                    style="background-size: contain !important; background-position: center !important;
                        background-repeat: no-repeat !important;
                        background-image: url({{ isset($image) && $image ? asset($image) : asset('images/default-dark.png') }});
                        border-radius: 8px; width: 100%; height: 200px; position: relative; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

                    <button type="button" class="remove-image" id="removeImage{{ $id }}"
                        style="display: {{ isset($image) && $image ? 'block' : 'none' }}; 
                        position: absolute; top: 10px; right: 10px; background-color: red; color: white; border: none; 
                        border-radius: 50%; padding: 5px 10px; font-size: 16px;">
                        <i class="fa fa-times"></i>
                    </button>

                </div>
            </div>
            <div class="avatar-edit mt-2">
                <x-adminlte-input type="file" class="profilePicUpload" name="{{ $name }}"
                    id="profilePicUpload{{ $id }}" accept=".png, .jpg, .jpeg"
                    onchange="previewImage(event, '{{ $id }}')" style="display: none;" />
                <label for="profilePicUpload{{ $id }}" class="btn btn-info btn-block btn-lg"
                    style="border-radius: 8px; font-size: 16px; padding: 12px 20px;">
                    Subir Imagen
                </label>
                <small class="d-block text-center text-muted mt-2">Soporta imágenes
                    jpeg, jpg, png</b></small>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event, id) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('imagePreview' + id);
            const removeBtn = document.getElementById('removeImage' + id);
            output.style.backgroundImage = 'url(' + reader.result + ')';
            output.style.backgroundSize = 'contain';
            output.style.backgroundPosition = 'center';
            output.style.backgroundRepeat = 'no-repeat';
            removeBtn.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    document.getElementById('removeImage{{ $id }}').addEventListener('click', function() {
        const output = document.getElementById('imagePreview{{ $id }}');
        const removeBtn = document.getElementById('removeImage{{ $id }}');
        output.style.backgroundImage = 'url({{ asset('images/default-dark.png') }})';
        output.style.backgroundSize = 'contain';
        output.style.backgroundPosition = 'center';
        output.style.backgroundRepeat = 'no-repeat';
        removeBtn.style.display = 'none';
        document.getElementById('profilePicUpload{{ $id }}').value = '';
        
        // Marcar para eliminar la imagen
        document.getElementById('removeLogoInput').value = '1';
    });
</script>
