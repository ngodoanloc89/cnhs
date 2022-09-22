<?php

namespace Botble\Blog\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\Blog\Forms\TypeForm;
use Botble\Blog\Http\Requests\TypeRequest;
use Botble\Blog\Repositories\Interfaces\TypeInterface;
use Botble\Blog\Tables\TypeTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Throwable;

class TypeController extends BaseController
{
    use HasDeleteManyItemsTrait;

    /**
     * @var TypeInterface
     */
    protected $typeRepository;

    /**
     * @param TypeInterface $typeRepository
     */
    public function __construct(TypeInterface $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    /**
     * @param TypeTable $dataTable
     * @return Factory|View
     *
     * @throws Throwable
     */
    public function index(TypeTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/blog::types.menu'));

        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle('Loại văn bản');

        return $formBuilder->create(TypeForm::class)->renderForm();
    }

    /**
     * @param TypeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(TypeRequest $request, BaseHttpResponse $response)
    {
        $type = $this->typeRepository->createOrUpdate(array_merge($request->input(), [
            'author_id'   => Auth::id(),
            'author_type' => User::class,
        ]));
        event(new CreatedContentEvent(TYPE_MODULE_SCREEN_NAME, $request, $type));

        return $response
            ->setPreviousUrl(route('types.index'))
            ->setNextUrl(route('types.edit', $type->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $type = $this->typeRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $type));

        page_title()->setTitle(trans('plugins/blog::types.edit') . ' "' . $type->name . '"');

        return $formBuilder->create(TypeForm::class, ['model' => $type])->renderForm();
    }

    /**
     * @param int $id
     * @param TypeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, TypeRequest $request, BaseHttpResponse $response)
    {
        $type = $this->typeRepository->findOrFail($id);
        $type->fill($request->input());

        $this->typeRepository->createOrUpdate($type);
        event(new UpdatedContentEvent(TYPE_MODULE_SCREEN_NAME, $request, $type));

        return $response
            ->setPreviousUrl(route('types.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $type = $this->typeRepository->findOrFail($id);
            $this->typeRepository->delete($type);

            event(new DeletedContentEvent(TYPE_MODULE_SCREEN_NAME, $request, $type));

            return $response->setMessage(trans('plugins/blog::types.deleted'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/blog::types.cannot_delete'));
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     *
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems($request, $response, $this->typeRepository, TYPE_MODULE_SCREEN_NAME);
    }

    /**
     * Get list types in db
     *
     * @return array
     */
    public function getAllTypes()
    {
        return $this->typeRepository->pluck('name');
    }
}
