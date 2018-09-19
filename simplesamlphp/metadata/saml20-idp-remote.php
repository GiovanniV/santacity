<?php
/**
 * SAML 2.0 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */

 $metadata['http://sso.santa-ana.org/adfs/services/trust'] = array (
   'entityid' => 'http://sso.santa-ana.org/adfs/services/trust',
   'contacts' =>
   array (
     0 =>
     array (
       'contactType' => 'support',
       'emailAddress' =>
       array (
         0 => '',
       ),
       'telephoneNumber' =>
       array (
         0 => '',
       ),
     ),
   ),
   'metadata-set' => 'saml20-idp-remote',
   'SingleSignOnService' =>
   array (
     0 =>
     array (
       'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
       'Location' => 'https://sso.santa-ana.org/adfs/ls/',
     ),
     1 =>
     array (
       'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
       'Location' => 'https://sso.santa-ana.org/adfs/ls/',
     ),
   ),
   'SingleLogoutService' =>
   array (
     0 =>
     array (
       'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
       'Location' => 'https://sso.santa-ana.org/adfs/ls/',
     ),
     1 =>
     array (
       'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
       'Location' => 'https://sso.santa-ana.org/adfs/ls/',
     ),
   ),
   'ArtifactResolutionService' =>
   array (
   ),
   'NameIDFormats' =>
   array (
     0 => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
     1 => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
     2 => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
   ),
   'keys' =>
   array (
     0 =>
     array (
       'encryption' => true,
       'signing' => false,
       'type' => 'X509Certificate',
       'X509Certificate' => 'MIIC5DCCAcygAwIBAgIQX/486XD9nqdLBV2uSA2dOjANBgkqhkiG9w0BAQsFADAuMSwwKgYDVQQDEyNBREZTIEVuY3J5cHRpb24gLSBzc28uc2FudGEtYW5hLm9yZzAeFw0xODAzMDgwMDM1MDhaFw0xOTAzMDgwMDM1MDhaMC4xLDAqBgNVBAMTI0FERlMgRW5jcnlwdGlvbiAtIHNzby5zYW50YS1hbmEub3JnMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtgAty0snHjPr4LxQp32PN9X0Mt3isKN52Vy2nuXPLy5zC7ZGwIw3KyApyXZOM0PiMwwtS9rZaXh3RVgnZXuDS6MjWx3BJ/iT9cPAm+FWol7Ly/crBp5bYj9ZpvMEVY7mbRI1GpRTkBUIsbNabwQYm8gR4rkC9eSqibPylbBH9yBgUuhPUrRU0LT9mryKjB3/Sfvr9EONtINCtSIOckDGKgbeA/YehCt3w9yc46cJT3pXE+lehcDEt7dXVE2gJa8od6eESL+E4kH4KkqrRA9tT1RbeNsZlEfB6vqhX8ulmdYv/n32GYSD6trPCwtPI3oGrb30xp0IGdebGT1ZvfvFuQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAT3hDYp1Kh99460yO7d8QPKjuMd1v7mg1BvrsswXC2zABK8ZRH5ZMUnByOrtdODDdjtAQl9t879NVqxeZBPFmw2ecbdLJQ/YkJQSJFHuzHsg51PzGUVdyHEZNqs50llJ67+otg2KgdKoS0/gWa4nwSSFzHHU1Tu0ayu7tix5Tq1OpQAaNq+HXwsv5NHb2nV9r8r3lAsfn++DUtkqOGZx27QKhYzRyv3HBWMbM7CYFg6PkehumBNtCTs6P8wf2w+nftVoGRzr+ewnU4hbR+FyzbsPV9VMaIj+DgbyMByaiMS5aHFIicTMg9Dfoyl6yttWIKIc6S2Tc+aoLxwiElFRBc',
     ),
     1 =>
     array (
       'encryption' => false,
       'signing' => true,
       'type' => 'X509Certificate',
       'X509Certificate' => 'MIIC3jCCAcagAwIBAgIQbFOe4lMmBaVLs5F6iepsuDANBgkqhkiG9w0BAQsFADArMSkwJwYDVQQDEyBBREZTIFNpZ25pbmcgLSBzc28uc2FudGEtYW5hLm9yZzAeFw0xODAzMDgwMDM1MDhaFw0xOTAzMDgwMDM1MDhaMCsxKTAnBgNVBAMTIEFERlMgU2lnbmluZyAtIHNzby5zYW50YS1hbmEub3JnMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAp81e3rUdEYPnMevIdQEBj9h+4tdRQqZQctYfL9Jh8YeE/WeFfo105HsqhpGsSlHcCd29bez25KqY6rG+iC+XBQ6R7zut76rNgcCaU8n4XVWxkDzBDEjhqwbO/8LM5dp+Ml7zUYpNvymq0fIJ64to77FcbMFQK59oDpFAb2ZSu38aSBIGLByn6sn1hS9WZlaXm9elcVCNBzXaJ+QtJC0CC1dWjROCA6otU3IfCG49FWMDWuYv1+sO69UIjpVHwUxYmr9bivGTvoh2nHCnfo5a6sSIgNq3eYBQLnatlL+wE6JyTnRX3bN1oQ9aB1UX/vkM0IA/opJ+eN5aUJTh/4+htwIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBUBjlib7P7CerEwFOPJrZDGEqGa1now4a5zYQdAFYC5iTnM58DzjV1LUIMmAml40lKBinkcMWvbtNKuvvHlLRpgtDFB671gKdCObdCGNZocMgGio9gJEA6XulHKTurBBqkllRJtgUbVUxyKvwg0kBfdSlvLZW54M0IzfpiaR1dXDOK+L3J81UZL8LxR1tQqDjOkjMb4UeVKrdyib50zfxoGVa2jQKaaiPDFpHdgi4W9XgP/Nu7zBFXHNA7l2qBBeospVInPRj6AYyRhPzT9Qga5u/SzYnkK2DAqqonJe38KeaRe4fJAetIqq5x3MSM9JUIpJuz07KwkWBYH+xoDKL6',
     ),
   ),
 );

