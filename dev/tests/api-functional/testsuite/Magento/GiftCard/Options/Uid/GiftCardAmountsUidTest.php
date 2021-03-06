<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GiftCard\Options\Uid;

use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test for giftcard amounts Uid
 */
class GiftCardAmountsUidTest extends GraphQlAbstract
{
    /**
     * @magentoApiDataFixture Magento/GiftCard/_files/gift_card_1.php
     */
    public function testQueryUidForGiftCardAmounts(): void
    {
        $productSku = 'gift-card-with-amount';
        $query = $this->getQuery($productSku);
        $response = $this->graphQlQuery($query);
        $responseProduct = $response['products']['items'][0];

        self::assertNotEmpty($responseProduct['giftcard_amounts']);

        foreach ($responseProduct['giftcard_amounts'] as $giftcardAmount) {
            $uid = $this->getUidByValue((int) $giftcardAmount['value']);
            self::assertEquals($uid, $giftcardAmount['uid']);
        }
    }

    /**
     * Get Uid by value
     *
     * @param int $value
     *
     * @return string
     */
    private function getUidByValue(int $value): string
    {
        $value = number_format($value, 4, '.', '');
        return base64_encode('giftcard/giftcard_amount/' . $value);
    }

    /**
     * Get query
     *
     * @param string $sku
     *
     * @return string
     */
    private function getQuery(string $sku): string
    {
        return <<<QUERY
query {
  products(filter: { sku: { eq: "$sku" } }) {
    items {
      sku
      ... on GiftCardProduct {
        giftcard_amounts {
          uid
          value_id
          website_id
          value
          attribute_id
          website_value
        }
        gift_card_options {
          title
          required
          ... on CustomizableFieldOption {
            value: value {
              uid
            }
          }
        }
      }
    }
  }
}
QUERY;
    }
}
