<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Создаем поле формы для загрузки файла
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image', // Текст, который будет использован в качестве метки
                'required' => false, // Установить в false, если файл не является обязательным
                'allow_delete' => true, // Установить в true, если нужно разрешить удаление файла
                'download_uri' => false, // Установить в false, чтобы не отображать ссылку для загрузки файла
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Связываем форму с классом Image
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}

