            <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="width: 100%;font-family: 'Poppins', sans-serif !important;">
                        <tbody>
                            <tr>
                                <td>
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                        style="background: #42a5f5 url({{ asset('frontend/images/templates/footer_bg.png') }}) repeat 0 0;width: 100%;text-align: center;">
                                        <tbody>
                                            <tr>
                                                <td style="height: 20px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="350px" style="width: 350px;margin: 0 auto">
                                                    {{-- <a href="{{ route('about') }}"
                                                        style="font-family: 'Poppins', sans-serif !important;font-weight: 500;font-size: 14px;color: #ffffff;text-decoration: none;margin-right: 20px;">About</a>
                                                    <a href="{{ route('blogs/index') }}"
                                                        style="font-family: 'Poppins', sans-serif !important;font-weight: 500;font-size: 14px;color: #ffffff;text-decoration: none;margin-right: 20px;">Blog</a>
                                                    <a href="{{route('contact_us')}}"
                                                        style="font-family: 'Poppins', sans-serif !important;font-weight: 500;font-size: 14px;color: #ffffff;text-decoration: none;">Contact
                                                        Us</a> --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="height: 20px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="350px" style="width: 350px;margin: 0 auto">
                                                    {{-- <a href="{{ route('cms_privacy') }}"
                                                        style="font-family: 'Poppins', sans-serif !important;font-weight: 500;font-size: 14px;color: #ffffff;text-decoration: none;margin-right: 20px;padding-right:20px;border-right: 1px solid #ffffff;">Privacy
                                                        Policy</a>
                                                    <a href="{{ route('cms_terms') }}"
                                                        style="font-family: 'Poppins', sans-serif !important;font-weight: 500;font-size: 14px;color: #ffffff;text-decoration: none;margin-right: 20px;padding-right:20px;border-right: 1px solid #ffffff;">Terms
                                                        of Services</a>
                                                    
                                                    <a href="{{ route('faq',['slug' =>  @$faqs[0]->frontendFaqs ? @$faqs[0]->frontendFaqs[0]->slug : 'not-found' ]) }}"
                                                        style="font-family: 'Poppins', sans-serif !important;font-weight: 500;font-size: 14px;color: #ffffff;text-decoration: none;">FAQ</a> --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="height: 20px;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                        style="background-color: #1a237e;width: 100%;font-family: 'Poppins', sans-serif !important;">
                                        <tr>
                                            <td style="height: 10px;"></td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="font-family: 'Poppins', sans-serif !important;font-weight: 500;font-size: 10px;color: #ffffff;text-align: center;">
                                                © {{ date('Y') }} All right reserved. {{config('app.name')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="height: 10px;"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
