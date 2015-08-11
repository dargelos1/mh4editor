<?php
namespace MH4Editor\MH4EditorBundle\Form;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class UploadedFileTransformer implements DataTransformerInterface
{
	public function reverseTransform($data){

		if(!$data){
			return null;
		}
		//echo $data;die;
		//var_dump($data);die;
		$path = $data['tmp_name'];
		$pathinfo = pathinfo($path);
		$basename = $pathinfo['basename'];

		try{

			$uploadedFile = new UploadedFile(
				$path,
				$basename,
				$data['type'],
				$data['size'],
				$data['error']
			);
		} catch(FileNotFoundException $ex){

			throw new TransformationFailedException($ex->getMessage());
		}

		return $uploadedFile;
	}

	public function transform($file){

		return $file;

	}
}