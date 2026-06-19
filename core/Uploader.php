<?php

namespace app\core;

/** Управление загрузками на сервер */
class Uploader {
    private const string IMAGE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
    private const array ALLOWED_FILE_TYPE_EXTENSIONS = [
        'image' => ['jpg', 'jpeg', 'png', 'webp'],
        'file' => ['doc', 'pdf', 'xls', 'xlsx', 'docx', 'ppt', 'pptx']
    ];
    private int $uploadMaxSize;
    public array $errors = [];
    public array $savedFileNames = [];


    public function __construct(private readonly Request $request, private readonly Env $env) {
        $this->uploadMaxSize = $this->env->get('UPLOAD_MAX_SIZE') ?? 5;
    }

    public function uploadFromFiles(string $fieldName, string $fileType = 'image', ?string $prefix = null): void {
        $files = $this->request->files($fieldName);
        $this->errors = [];
        $this->savedFileNames = [];

        if(empty($files)) return;

        $fileCount = is_array($files['tmp_name']) ? count($files['tmp_name']) : 1;

        for ($i = 0; $i < $fileCount; $i++) {
            $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
            $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];

            if (empty($tmpName) || $fileError === UPLOAD_ERR_NO_FILE) {
                $this->errors[] = "Пустой файл. Код ошибки: {$fileError}";
                continue;
            }

            if ($fileError !== UPLOAD_ERR_OK) {
                $this->errors[] = "Ошибка загрузки файла {$fileName}. Код ошибки: {$fileError}";
                continue;
            }

            if ($fileSize > $this->uploadMaxSize * 1024 * 1024) {
                $this->errors[] = "Файл {$fileName} слишком большой. Максимум " . $this->uploadMaxSize . " МБ.";
                continue;
            }

            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($extension, self::ALLOWED_FILE_TYPE_EXTENSIONS[$fileType])) {
                $this->errors[] = "Файл {$fileName} имеет недопустимый формат. Разрешены: "
                    . implode(', ', self::ALLOWED_FILE_TYPE_EXTENSIONS[$fileType]);

                continue;
            }

            $newFileName = md5_file($tmpName) . '.' . $extension;

            if ($prefix) {
                $newFileName = $prefix . '_' . $newFileName;
            }

            $destination = self::IMAGE_PATH . $newFileName;

            if (file_exists($destination)) {
                $this->errors[] = "Файл {$fileName} уже существует!. Имя на сервере - {$newFileName}";
                $this->savedFileNames[] = $newFileName;

                continue;
            }

            if (move_uploaded_file($tmpName, $destination)) {
                $this->savedFileNames[] = $newFileName;

            } else {
                $this->errors[] = "Не удалось сохранить файл {$fileName} на сервере.";
            }
        }
    }
}