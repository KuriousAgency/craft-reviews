<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\elements;

use kuriousagency\reviews\Reviews;
use kuriousagency\reviews\records\Review as ReviewRecord;
use kuriousagency\reviews\elements\db\ReviewQuery;
use kuriousagency\reviews\elements\actions\DeleteReview;
use kuriousagency\reviews\elements\actions\EnableReview;
use kuriousagency\reviews\elements\actions\DisableReview;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Order;
use craft\commerce\elements\Product;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\ElementHelper;
use craft\helpers\UrlHelper;
use craft\validators\DateTimeValidator;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class Review extends Element
{
	// Constants
    // =========================================================================

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';
	
	// Public Properties
    // =========================================================================

    /**
     * @var string
     */
	public $feedback;
	public $reply;
	public $rating;
	public $customerId;
	public $purchasableId;
	public $purchasableType;
	public $enabled;
	public $orderId;

	private $_email;
	private $_firstName;
	private $_lastName;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('reviews', '');
	}
	
	public function __toString()
    {
        return substr($this->feedback, 0, 25) . (strlen($this->feedback) > 25 ? '...' : '');
    }

    /**
     * @inheritdoc
     */
    public static function hasContent(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function hasTitles(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function isLocalized(): bool
    {
        return false;
	}
	
	public static function hasStatuses(): bool
	{
		return true;
	}

	public static function statuses(): array
	{
		return [
			self::STATUS_ENABLED => Craft::t('reviews', 'Enabled'),
            self::STATUS_DISABLED => Craft::t('reviews', 'Disabled'),
		];
	}

	public function getStatus()
	{
		//$status = parent::getStatus();

		if ($this->enabled) {
			return self::STATUS_ENABLED;
		}

		return self::STATUS_DISABLED;
	}

	/*public static function eagerLoadingMap(array $sourceElements, string $handle)
    {
        if ($handle == 'variants') {
            $sourceElementIds = ArrayHelper::getColumn($sourceElements, 'id');

            $map = (new Query())
                ->select('productId as source, id as target')
                ->from(['{{%commerce_variants}}'])
                ->where(['in', 'productId', $sourceElementIds])
                ->orderBy('sortOrder asc')
                ->all();

            return [
                'elementType' => Variant::class,
                'map' => $map
            ];
        }

        return parent::eagerLoadingMap($sourceElements, $handle);
    }

    
    public static function prepElementQueryForTableAttribute(ElementQueryInterface $elementQuery, string $attribute)
    {
        
        if ($attribute === 'variants') {
            $with = $elementQuery->with ?: [];
            $with[] = 'variants';
            $elementQuery->with = $with;
        } else {
            parent::prepElementQueryForTableAttribute($elementQuery, $attribute);
        }
	}*/

    /**
     * @inheritdoc
     */
    public static function find(): ElementQueryInterface
    {
        return new ReviewQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    protected static function defineSources(string $context = null): array
    {
        $sources = [
			'*' => [
                'key' => '*',
				'label' => Craft::t('reviews', 'All Reviews'),
				'defaultSort' => ['dateCreated', 'desc'],
			],
			'General' => [
                'key' => 'general',
				'label' => Craft::t('reviews', 'General Reviews'),
				'criteria' => ['purchasableId' => ':empty:'],
				'defaultSort' => ['dateCreated', 'desc'],
            ]
		];

        $allPurchasables = Commerce::getInstance()->getPurchasables()->getAllPurchasableElementTypes();
 
 		foreach ($allPurchasables as $purchasable) {
            $instance = new $purchasable;
			$sources[$instance->displayName()] = [
				'key' => $purchasable,
				'label' => Craft::t('reviews', $instance->displayName() . ' Reviews'),
				'criteria' => ['purchasableType' => $purchasable],
				'defaultSort' => ['dateCreated', 'desc']
			];
        }

        return $sources;
	}
	
	protected static function defineActions(string $source = null): array
	{
		$actions = [];

		$actions[] = EnableReview::class;
		$actions[] = DisableReview::class;

		$deleteAction = Craft::$app->getElements()->createAction([
			'type' => DeleteReview::class,
			'confirmationMessage' => Craft::t('reviews', 'Are you sure you want to delete the selected reviews?'),
			'successMessage' => Craft::t('reviews', 'Reviews deleted.'),
		]);
		$actions[] = $deleteAction;

		return $actions;
	}

	protected static function defineSortOptions(): array
	{
		return [
			'dateCreated' => Craft::t('reviews', 'Date Created'),
			'rating' => Craft::t('reviews', 'Rating'),
			'email' => Craft::t('reviews', 'Email'),
			//'product' => ['label' => Craft::t('reviews', 'Product'), 'attribute' => 'product.title'],
			'firstName' => Craft::t('reviews', 'Firstname'),
			'lastName' => Craft::t('reviews', 'Lastname'),	
		];
	}

	protected static function defineTableAttributes(): array
    {
		return [
			'feedback' => ['label' => Craft::t('reviews', 'Feedback')],
			'rating' => ['label' => Craft::t('reviews', 'Rating')],
			'email' => ['label' => Craft::t('reviews', 'Email')],
			'firstName' => ['label' => Craft::t('reviews', 'Firstname')],
			'lastName' => ['label' => Craft::t('reviews', 'Lastname')],
			'email' => ['label' => Craft::t('reviews', 'Email')],
			'purchasable' => ['label' => Craft::t('reviews', 'Purchasable')],
			'purchasableType' => ['label' => Craft::t('reviews', 'PurchasableType')],
			'order' => ['label' => Craft::t('reviews', 'Order')],
			'reply' => ['label' => Craft::t('reviews', 'Replied?')],
			'dateCreated' => ['label' => Craft::t('reviews', 'Date Created')],
			'dateUpdated' => ['label' => Craft::t('reviews', 'Date Updated')],
		];
	}

	protected static function defineDefaultTableAttributes(string $source): array
    {
		return [
			'feedback',
			'rating',
			'email',
			'purchasable',
			'dateCreated',
		];
	}

	protected static function defineSearchableAttributes(): array
    {
		return [
			'rating',
			'email',
			'firstName',
			'lastName',
			'dateCreated',
			'purchasable',
			'order',
			'feedback',
			'reply'
		];
	}

	public function getSearchKeywords(string $attribute): string
    {
        switch ($attribute) {
            case 'purchasable':
                return $this->purchasable->title ?? '';
            case 'order':
                return $this->order->reference ?? '';
            default:
                return parent::getSearchKeywords($attribute);
        }
    }

	protected function tableAttributeHtml(string $attribute): string
    {
		switch ($attribute) {
			case 'rating':
				{
					$stars = '';
					for ($i=0; $i < $this->rating; $i++) { 
						$stars .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#F2DC31" viewBox="0 0 24 24" stroke="none">
										<path id="mod040reviewStar" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
									</svg>';
					}
					return $stars;
				}
			case 'purchasable':
				{
					if (!$this->purchasableId) {
						return '';
					}
					return '<a href="'.$this->purchasable->cpEditUrl.'"><span class="status '.$this->purchasable->status.'"></span>'.$this->purchasable->description.'</a>';
				}
			case 'order':
				{
					if (!$this->orderId) {
						return '';
					}
					return '<a href="'.$this->order->cpEditUrl.'"><span class="status '.$this->order->status.'"></span>'.$this->order->reference.'</a>';
				}
			case 'reply':
				{
					return $this->reply ? '<span data-icon="check" title="Yes"></span>' : '';
				}
			default:
                {
                    return parent::tableAttributeHtml($attribute);
                }
		}
	}

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
		return [
			[['rating', 'customerId', 'purchasableId', 'orderId'], 'number', 'integerOnly' => true],
			[['enabled'], 'boolean'],
			['enabled', 'default', 'value' => false],
			[['feedback', 'email'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIsEditable(): bool
    {
        return true;
	}

	public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('reviews/' . $this->id);
    }
	
	public function getCustomer()
	{
		if (!$this->customerId) {
			return null;
		}
		return Commerce::getInstance()->getCustomers()->getCustomerById($this->customerId);
	}

	public function getOrder()
	{
		if (!$this->orderId) {
			return null;
		}
		return Commerce::getInstance()->getOrders()->getOrderById($this->orderId);
	}

	public function getPurchasable()
	{
		if (!$this->purchasableId) {
			return null;
		}
		return Commerce::getInstance()->getPurchasables()->getPurchasableById($this->purchasableId);
	}

	public function getVerifiedBuyer(): bool
	{
		return $this->orderId ? true : false;
	}


	public function getEmail(): string
	{
		if ($this->getCustomer() && $this->getCustomer()->getUser()) {
			$this->setEmail($this->getCustomer()->getUser()->email);
		}

		return $this->_email ?? '';
	}

	public function setEmail($value)
    {
        $this->_email = $value;
	}
	
	public function getFirstName(): string
	{
		if ($this->getCustomer() && $this->getCustomer()->getUser()) {
			$this->setFirstName($this->getCustomer()->getUser()->firstName);
		} elseif ($this->getOrder()) {
			$this->setFirstName($this->getOrder()->billingAddress->firstName);
		}

		return $this->_firstName ?? '';
	}

	public function setFirstName($value)
    {
        $this->_firstName = $value;
	}
	
	public function getLastName(): string
	{
		if ($this->getCustomer() && $this->getCustomer()->getUser()) {
			$this->setLastName($this->getCustomer()->getUser()->lastName);
		} elseif ($this->getOrder()) {
			$this->setLastName($this->getOrder()->billingAddress->lastName);
		}

		return $this->_lastName ?? '';
	}

	public function setLastName($value)
    {
        $this->_lastName = $value;
    }



    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    /*public function getEditorHtml(): string
    {
        $html = Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => Craft::t('app', 'Title'),
                'siteId' => $this->siteId,
                'id' => 'title',
                'name' => 'title',
                'value' => $this->title,
                'errors' => $this->getErrors('title'),
                'first' => true,
                'autofocus' => true,
                'required' => true
            ]
        ]);

        $html .= parent::getEditorHtml();

        return $html;
    }*/

    // Events
    // -------------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function beforeSave(bool $isNew): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave(bool $isNew)
    {
		if (!$isNew) {
            $record = ReviewRecord::findOne($this->id);

            if (!$record) {
                throw new Exception('Invalid review ID: ' . $this->id);
            }
        } else {
            $record = new ReviewRecord();
            $record->id = $this->id;
		}
		
		$record->feedback = $this->feedback;
		$record->reply = $this->reply;
		$record->rating = $this->rating;
		$record->customerId = $this->customerId;
		$record->purchasableId = $this->purchasableId;
		$record->purchasableType = $this->purchasableType;
		$record->orderId = $this->orderId;
		$record->email = $this->getEmail();
		$record->firstName = $this->getFirstName();
		$record->lastName = $this->getLastName();
		$record->enabled = $this->enabled;
// Craft::dd($record);
		$record->save();

		$this->id = $record->id;

		return parent::afterSave($isNew);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
    }


}
